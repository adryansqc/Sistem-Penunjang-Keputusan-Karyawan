<?php

namespace App\Filament\Pages;

use App\Models\AhpResult;
use App\Models\Evaluasi;
use App\Models\Karyawan;
use App\Models\Kriteria;
use App\Models\TopsisResult; // Import model TopsisResult
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class HasilEvaluasi extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;
    protected static ?string $navigationLabel = 'Hasil Evaluasi';
    protected static ?string $title = 'Hasil Evaluasi Karyawan (AHP & TOPSIS)'; // Update title
    protected static string|UnitEnum|null $navigationGroup = 'Evaluasi';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.hasil-evaluasi';

    public array $karyawanList = [];
    public array $kriteriaList = [];
    public array $combinedResults = []; // Properti baru untuk hasil gabungan

    // Define a threshold for contract extension eligibility
    protected float $eligibilityThreshold = 0.6; // You can adjust this value as needed

    public function mount(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $this->karyawanList = Karyawan::all()->toArray();
        $this->kriteriaList = Kriteria::all()->toArray();

        // Load AHP and TOPSIS results, keyed by karyawan_id for easy lookup
        $ahpResults = AhpResult::with('karyawan')->get()->keyBy('karyawan_id');
        $topsisResults = TopsisResult::with('karyawan')->get()->keyBy('karyawan_id');

        $combined = [];
        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            $combined[$karyawanId] = [
                'karyawan' => $karyawan,
                'ahp' => $ahpResults->get($karyawanId)?->toArray(), // Get AHP result if exists
                'topsis' => $topsisResults->get($karyawanId)?->toArray(), // Get TOPSIS result if exists
            ];
        }
        $this->combinedResults = $combined;
    }

    public function calculateAndSaveAHPResults(): void
    {
        if (empty($this->karyawanList) || empty($this->kriteriaList)) {
            Notification::make()
                ->title('Data karyawan atau kriteria tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        // Fetch all evaluations efficiently
        $evaluations = Evaluasi::all()->groupBy('karyawan_id')->map(function ($item) {
            return $item->keyBy('kriteria_id');
        });

        // Fetch criteria weights
        $kriteriaWeights = collect($this->kriteriaList)->keyBy('id')->map(fn($k) => (float)$k['bobot']);

        $calculatedScores = [];

        foreach ($this->karyawanList as $karyawan) {
            $totalScore = 0;
            $karyawanId = $karyawan['id'];

            foreach ($this->kriteriaList as $kriteria) {
                $kriteriaId = $kriteria['id'];
                $evaluationValue = $evaluations[$karyawanId][$kriteriaId]->nilai ?? 0; // Default to 0 if no evaluation
                $kriteriaWeight = $kriteriaWeights[$kriteriaId] ?? 0; // Default to 0 if no weight

                $totalScore += ($evaluationValue * $kriteriaWeight);
            }
            $calculatedScores[$karyawanId] = [
                'karyawan_id' => $karyawanId,
                'skor_total' => $totalScore,
                'karyawan_nama' => $karyawan['nama'], // For display purposes
            ];
        }

        // Sort by score in descending order to determine rank
        usort($calculatedScores, function ($a, $b) {
            return $b['skor_total'] <=> $a['skor_total'];
        });

        DB::beginTransaction();
        try {
            $rank = 1;
            foreach ($calculatedScores as &$scoreData) {
                $keterangan = ($scoreData['skor_total'] >= $this->eligibilityThreshold)
                    ? 'Layak Perpanjang Kontrak'
                    : 'Tidak Layak Perpanjang Kontrak';

                AhpResult::updateOrCreate(
                    ['karyawan_id' => $scoreData['karyawan_id']],
                    [
                        'skor_total' => $scoreData['skor_total'],
                        'peringkat' => $rank,
                        'keterangan' => $keterangan,
                    ]
                );
                $rank++;
            }
            DB::commit();

            // Reload data from the database to ensure 'karyawan' relationship is loaded
            $this->loadData();

            // Notification::make()
            //     ->title('Hasil AHP berhasil dihitung dan disimpan.')
            //     ->success()
            //     ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            // Notification::make()
            //     ->title('Gagal menghitung atau menyimpan hasil AHP.')
            //     ->body($e->getMessage())
            //     ->danger()
            //     ->send();
        }
    }

    public function calculateAndSaveTopsisResults(): void
    {
        if (empty($this->karyawanList) || empty($this->kriteriaList)) {
            Notification::make()
                ->title('Data karyawan atau kriteria tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        // 1. Ambil data evaluasi dan bobot kriteria
        $evaluations = Evaluasi::all()->groupBy('karyawan_id')->map(function ($item) {
            return $item->keyBy('kriteria_id');
        });

        $kriteriaWeights = collect($this->kriteriaList)->keyBy('id')->map(fn($k) => (float)$k['bobot']);

        $kriteriaTypes = collect($this->kriteriaList)->keyBy('id')->map(fn($k) => 'benefit'); // Default to benefit

        // Buat Decision Matrix (X)
        $decisionMatrix = []; // [karyawan_id][kriteria_id] = nilai_evaluasi
        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            foreach ($this->kriteriaList as $kriteria) {
                $kriteriaId = $kriteria['id'];
                $decisionMatrix[$karyawanId][$kriteriaId] = $evaluations[$karyawanId][$kriteriaId]->nilai ?? 0;
            }
        }

        $normalizedMatrix = [];
        $divisor = []; // Pembagi untuk normalisasi (akar kuadrat dari jumlah kuadrat setiap kolom)

        foreach ($this->kriteriaList as $kriteria) {
            $kriteriaId = $kriteria['id'];
            $sumOfSquares = 0;
            foreach ($this->karyawanList as $karyawan) {
                $karyawanId = $karyawan['id'];
                $sumOfSquares += pow($decisionMatrix[$karyawanId][$kriteriaId], 2);
            }
            $divisor[$kriteriaId] = sqrt($sumOfSquares);
        }

        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            foreach ($this->kriteriaList as $kriteria) {
                $kriteriaId = $kriteria['id'];
                if ($divisor[$kriteriaId] != 0) {
                    $normalizedMatrix[$karyawanId][$kriteriaId] = $decisionMatrix[$karyawanId][$kriteriaId] / $divisor[$kriteriaId];
                } else {
                    $normalizedMatrix[$karyawanId][$kriteriaId] = 0; // Hindari pembagian dengan nol
                }
            }
        }

        // 3. Matriks Normalisasi Terbobot (Y)
        $weightedNormalizedMatrix = [];
        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            foreach ($this->kriteriaList as $kriteria) {
                $kriteriaId = $kriteria['id'];
                $weightedNormalizedMatrix[$karyawanId][$kriteriaId] = $normalizedMatrix[$karyawanId][$kriteriaId] * $kriteriaWeights[$kriteriaId];
            }
        }

        $positiveIdeal = []; // A+
        $negativeIdeal = []; // A-

        foreach ($this->kriteriaList as $kriteria) {
            $kriteriaId = $kriteria['id'];
            $columnValues = array_column($weightedNormalizedMatrix, $kriteriaId);

            if ($kriteriaTypes[$kriteriaId] === 'benefit') {
                $positiveIdeal[$kriteriaId] = max($columnValues);
                $negativeIdeal[$kriteriaId] = min($columnValues);
            } else { // 'cost'
                $positiveIdeal[$kriteriaId] = min($columnValues);
                $negativeIdeal[$kriteriaId] = max($columnValues);
            }
        }

        // 5. Menghitung Jarak ke Solusi Ideal Positif (D+) dan Negatif (D-)
        $distancePositive = []; // D+
        $distanceNegative = []; // D-

        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            $sumSqPositive = 0;
            $sumSqNegative = 0;
            foreach ($this->kriteriaList as $kriteria) {
                $kriteriaId = $kriteria['id'];
                $sumSqPositive += pow($weightedNormalizedMatrix[$karyawanId][$kriteriaId] - $positiveIdeal[$kriteriaId], 2);
                $sumSqNegative += pow($weightedNormalizedMatrix[$karyawanId][$kriteriaId] - $negativeIdeal[$kriteriaId], 2);
            }
            $distancePositive[$karyawanId] = sqrt($sumSqPositive);
            $distanceNegative[$karyawanId] = sqrt($sumSqNegative);
        }

        // 6. Menghitung Nilai Preferensi (V)
        $topsisScores = []; // V
        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            if (($distancePositive[$karyawanId] + $distanceNegative[$karyawanId]) != 0) {
                $topsisScores[$karyawanId] = $distanceNegative[$karyawanId] / ($distancePositive[$karyawanId] + $distanceNegative[$karyawanId]);
            } else {
                $topsisScores[$karyawanId] = 0; // Hindari pembagian dengan nol
            }
        }

        // Siapkan data untuk penyimpanan dan peringkat
        $calculatedResults = [];
        foreach ($this->karyawanList as $karyawan) {
            $karyawanId = $karyawan['id'];
            $calculatedResults[$karyawanId] = [
                'karyawan_id' => $karyawanId,
                'skor_topsis' => $topsisScores[$karyawanId],
                'karyawan_nama' => $karyawan['nama'], // For display purposes
            ];
        }

        // Urutkan berdasarkan skor TOPSIS secara menurun untuk menentukan peringkat
        usort($calculatedResults, function ($a, $b) {
            return $b['skor_topsis'] <=> $a['skor_topsis'];
        });

        DB::beginTransaction();
        try {
            $rank = 1;
            foreach ($calculatedResults as &$scoreData) {
                $keterangan = ($scoreData['skor_topsis'] >= $this->eligibilityThreshold)
                    ? 'Layak Perpanjang Kontrak'
                    : 'Tidak Layak Perpanjang Kontrak';

                TopsisResult::updateOrCreate(
                    ['karyawan_id' => $scoreData['karyawan_id']],
                    [
                        'skor_topsis' => $scoreData['skor_topsis'],
                        'peringkat' => $rank,
                        'keterangan' => $keterangan,
                    ]
                );
                $rank++;
            }
            DB::commit();

            $this->loadData();

            // Notification::make()
            //     ->title('Hasil TOPSIS berhasil dihitung dan disimpan.')
            //     ->success()
            //     ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            // Notification::make()
            //     ->title('Gagal menghitung atau menyimpan hasil TOPSIS.')
            //     ->body($e->getMessage())
            //     ->danger()
            //     ->send();
        }
    }

    /**
     * Calculates and saves both AHP and TOPSIS results.
     */
    public function calculateAndSaveAllResults(): void
    {
        try {
            $this->calculateAndSaveAHPResults();
            $this->calculateAndSaveTopsisResults();

            Notification::make()
                ->title('Semua hasil evaluasi (AHP & TOPSIS) berhasil dihitung dan disimpan.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menghitung atau menyimpan semua hasil evaluasi.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

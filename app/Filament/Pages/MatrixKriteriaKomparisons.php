<?php

namespace App\Filament\Pages;

use App\Models\Kriteria;
use App\Models\Kriteriakomparison; // Pastikan nama model ini benar
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class MatrixKriteriaKomparisons extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentChartBar;

    protected string $view = 'filament.pages.matrix-kriteria-komparisons';

    protected static ?string $title = 'Matriks Perbandingan Kriteria';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4;

    public array $kriteriaList = [];
    public array $comparisonMatrix = [];
    public array $weights = [];

    public function mount(): void
    {
        $this->loadKriteriaAndMatrix();
        $this->calculateWeights();
    }

    protected function loadKriteriaAndMatrix(): void
    {
        $this->kriteriaList = Kriteria::orderBy('id')->get()->toArray();
        $this->comparisonMatrix = [];

        foreach ($this->kriteriaList as $k1) {
            foreach ($this->kriteriaList as $k2) {
                $comparison = Kriteriakomparison::where('kriteria1_id', $k1['id'])
                    ->where('kriteria2_id', $k2['id'])
                    ->first();
                $this->comparisonMatrix[$k1['id']][$k2['id']] = $comparison ? $comparison->nilai : 1.00;
            }
        }
    }

    public function updateComparisonValue(int $kriteria1Id, int $kriteria2Id, float $value): void
    {
        if ($value <= 0) {
            Notification::make()
                ->title('Nilai perbandingan harus lebih besar dari 0.')
                ->danger()
                ->send();
            return;
        }

        $this->comparisonMatrix[$kriteria1Id][$kriteria2Id] = $value;
        $this->comparisonMatrix[$kriteria2Id][$kriteria1Id] = 1 / $value;

        DB::beginTransaction();
        try {
            Kriteriakomparison::updateOrCreate(
                ['kriteria1_id' => $kriteria1Id, 'kriteria2_id' => $kriteria2Id],
                ['nilai' => $value]
            );
            Kriteriakomparison::updateOrCreate(
                ['kriteria1_id' => $kriteria2Id, 'kriteria2_id' => $kriteria1Id],
                ['nilai' => 1 / $value]
            );
            DB::commit();

            $this->calculateWeights();
            Notification::make()
                ->title('Nilai perbandingan berhasil diperbarui.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal memperbarui nilai perbandingan.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function calculateWeights(): void
    {
        $numKriteria = count($this->kriteriaList);
        if ($numKriteria === 0) {
            $this->weights = [];
            return;
        }

        $sumColumns = [];
        foreach ($this->kriteriaList as $k2) {
            $sumColumns[$k2['id']] = 0;
            foreach ($this->kriteriaList as $k1) {
                $sumColumns[$k2['id']] += $this->comparisonMatrix[$k1['id']][$k2['id']];
            }
        }

        $normalizedMatrix = [];
        foreach ($this->kriteriaList as $k1) {
            foreach ($this->kriteriaList as $k2) {
                if ($sumColumns[$k2['id']] != 0) {
                    $normalizedMatrix[$k1['id']][$k2['id']] = $this->comparisonMatrix[$k1['id']][$k2['id']] / $sumColumns[$k2['id']];
                } else {
                    $normalizedMatrix[$k1['id']][$k2['id']] = 0;
                }
            }
        }

        $this->weights = [];
        foreach ($this->kriteriaList as $k1) {
            $rowSum = 0;
            foreach ($this->kriteriaList as $k2) {
                $rowSum += $normalizedMatrix[$k1['id']][$k2['id']];
            }
            $this->weights[$k1['id']] = $rowSum / $numKriteria;
        }

        // Simpan bobot ke model Kriteria
        foreach ($this->kriteriaList as $kriteriaData) {
            $kriteria = Kriteria::find($kriteriaData['id']);
            if ($kriteria) {
                $kriteria->bobot = $this->weights[$kriteria->id] ?? 0;
                $kriteria->save();
            }
        }

        // Hapus notifikasi dari sini
        // Notification::make()
        //     ->title('Bobot kriteria berhasil dihitung dan disimpan.')
        //     ->success()
        //     ->send();
    }

    /**
     * Triggers the weight calculation and sends a notification.
     */
    public function triggerCalculateWeightsAndNotify(): void
    {
        $this->calculateWeights();

        Notification::make()
            ->title('Bobot kriteria berhasil dihitung dan disimpan.')
            ->success()
            ->send();
    }

    /**
     * Updates the entire comparison matrix by ensuring all pairs exist.
     * This method will create missing comparison entries with a default value of 1.00.
     * It will NOT overwrite existing user-defined comparison values.
     */
    public function updateMatrixData(): void
    {
        DB::beginTransaction();
        try {
            $allKriteria = Kriteria::all();

            foreach ($allKriteria as $k1) {
                foreach ($allKriteria as $k2) {
                    // Pastikan perbandingan diri sendiri adalah 1
                    if ($k1->id === $k2->id) {
                        Kriteriakomparison::updateOrCreate(
                            ['kriteria1_id' => $k1->id, 'kriteria2_id' => $k2->id],
                            ['nilai' => 1.00]
                        );
                    } else {
                        // Hanya buat entri jika belum ada, agar tidak menimpa input pengguna
                        $existingComparison = Kriteriakomparison::where('kriteria1_id', $k1->id)
                            ->where('kriteria2_id', $k2->id)
                            ->first();
                        if (!$existingComparison) {
                            Kriteriakomparison::create([
                                'kriteria1_id' => $k1->id,
                                'kriteria2_id' => $k2->id,
                                'nilai' => 1.00, // Nilai default
                            ]);
                        }
                    }
                }
            }
            DB::commit();

            $this->loadKriteriaAndMatrix(); // Muat ulang matriks dari DB
            $this->calculateWeights(); // Hitung ulang bobot

            Notification::make()
                ->title('Data matriks perbandingan berhasil diperbarui.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal memperbarui data matriks perbandingan.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Evaluasi;
use App\Models\Karyawan;
use App\Models\Kriteria;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class EvaluasiKaryawan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentMagnifyingGlass;
    protected static ?string $navigationLabel = 'Evaluasi Karyawan';
    protected static ?string $title = 'Evaluasi Karyawan';
    protected static string|UnitEnum|null $navigationGroup = 'Evaluasi';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.evaluasi-karyawan';

    public $karyawanList = [];
    public $kriteriaList = [];
    public $evaluationValues = [];

    public function mount(): void
    {
        $this->karyawanList = Karyawan::select('id', 'nama', 'posisi')->get()->toArray();
        $this->kriteriaList = Kriteria::select('id', 'nama', 'bobot')->get()->toArray();
        $existingEvaluations = Evaluasi::all();
        foreach ($existingEvaluations as $eval) {
            $this->evaluationValues[$eval->karyawan_id][$eval->kriteria_id] = $eval->nilai;
        }
    }

    public function updateNilai($karyawanId, $kriteriaId, $value): void
    {
        if (!is_numeric($value) || $value < 0 || $value > 1) {
            Notification::make()
                ->title('Nilai harus antara 0.0 dan 1.0')
                ->danger()
                ->send();
            return;
        }

        try {
            Evaluasi::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId,
                    'kriteria_id' => $kriteriaId,
                ],
                ['nilai' => $value]
            );

            $this->evaluationValues[$karyawanId][$kriteriaId] = $value;

            Notification::make()
                ->title('Nilai berhasil disimpan')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan nilai')
                ->danger()
                ->send();
        }
    }

    public function simpanEvaluasi(): void
    {
        try {
            DB::beginTransaction();

            foreach ($this->evaluationValues as $karyawanId => $kriteriaValues) {
                foreach ($kriteriaValues as $kriteriaId => $nilai) {
                    Evaluasi::updateOrCreate(
                        [
                            'karyawan_id' => $karyawanId,
                            'kriteria_id' => $kriteriaId,
                        ],
                        ['nilai' => $nilai]
                    );
                }
            }

            DB::commit();

            Notification::make()
                ->title('Semua nilai evaluasi berhasil disimpan')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal menyimpan evaluasi')
                ->danger()
                ->send();
        }
    }
}

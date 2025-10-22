<?php

namespace App\Observers;

use App\Models\Kriteria;
use App\Models\Kriteriakomparison; // Pastikan nama model ini benar (Kriteriakomparison atau KriteriaKomparison)

class KriteriaObserver
{
    public function created(Kriteria $kriteria): void
    {
        // Buat perbandingan diri sendiri (kriteria vs kriteria)
        Kriteriakomparison::create([
            'kriteria1_id' => $kriteria->id,
            'kriteria2_id' => $kriteria->id,
            'nilai' => 1.00,
        ]);

        // Ambil semua kriteria lain yang sudah ada (kecuali kriteria yang baru dibuat ini)
        $existingKriterias = Kriteria::where('id', '!=', $kriteria->id)->get();

        foreach ($existingKriterias as $existingKriteria) {
            // Buat perbandingan kriteria baru dengan kriteria yang sudah ada
            Kriteriakomparison::create([
                'kriteria1_id' => $kriteria->id,
                'kriteria2_id' => $existingKriteria->id,
                'nilai' => 1.00, // Nilai default
            ]);

            // Buat perbandingan kriteria yang sudah ada dengan kriteria baru (invers)
            Kriteriakomparison::create([
                'kriteria1_id' => $existingKriteria->id,
                'kriteria2_id' => $kriteria->id,
                'nilai' => 1.00, // Nilai default
            ]);
        }
    }

    /**
     * Handle the Kriteria "deleted" event.
     * Hapus semua perbandingan yang terkait jika kriteria dihapus.
     */
    public function deleted(Kriteria $kriteria): void
    {
        Kriteriakomparison::where('kriteria1_id', $kriteria->id)
            ->orWhere('kriteria2_id', $kriteria->id)
            ->delete();
    }

    // Anda bisa menambahkan metode lain seperti updated, restored, forceDeleted jika diperlukan
}

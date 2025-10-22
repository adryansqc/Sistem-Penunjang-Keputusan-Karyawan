<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriterias = [
            [
                'kode' => 'K1',
                'nama' => 'Produktivitas',
                'deskripsi' => 'Kemampuan menghasilkan output tinggi dan efisien.',
            ],
            [
                'kode' => 'K2',
                'nama' => 'Kualitas Kerja',
                'deskripsi' => 'Ketelitian, kerapian, dan ketepatan dalam menyelesaikan pekerjaan.',
            ],
            [
                'kode' => 'K3',
                'nama' => 'Kedisiplinan',
                'deskripsi' => 'Ketaatan terhadap aturan dan kehadiran.',
            ],
            [
                'kode' => 'K4',
                'nama' => 'Kerjasama Tim',
                'deskripsi' => 'Kemampuan bekerja sama dengan rekan tim untuk mencapai tujuan.',
            ],
        ];

        foreach ($kriterias as $kriteria) {
            \App\Models\Kriteria::create($kriteria);
        }
    }
}

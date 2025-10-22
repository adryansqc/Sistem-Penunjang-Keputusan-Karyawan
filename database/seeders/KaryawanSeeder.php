<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = [
            ['nama' => 'Andi Saputra', 'posisi' => 'Staff Produksi', 'masa_kontrak_bulan' => 12, 'tanggal_masuk' => '2020-02-15'],
            ['nama' => 'Budi Santoso', 'posisi' => 'Staff Gudang', 'masa_kontrak_bulan' => 12, 'tanggal_masuk' => '2019-05-10'],
            ['nama' => 'Citra Lestari', 'posisi' => 'Admin', 'masa_kontrak_bulan' => 12, 'tanggal_masuk' => '2021-08-01'],
        ];
        foreach ($karyawans as $karyawan) {
            Karyawan::create($karyawan);
        }
    }
}

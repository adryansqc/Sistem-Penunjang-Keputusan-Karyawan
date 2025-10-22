<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaKomparationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = DB::table('kriterias')->get();

        foreach ($criteria as $c1) {
            foreach ($criteria as $c2) {
                DB::table('kriteriakomparisons')->insert([
                    'kriteria1_id' => $c1->id,
                    'kriteria2_id' => $c2->id,
                    'nilai' => 1.00,
                ]);
            }
        }
    }
}

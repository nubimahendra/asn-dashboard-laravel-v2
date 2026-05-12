<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefIuranEselonSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['eselon_key' => 'IV/b', 'label' => 'Eselon IV/b', 'besaran' => 25000],
            ['eselon_key' => 'IV/a', 'label' => 'Eselon IV/a', 'besaran' => 30000],
            ['eselon_key' => 'III/b', 'label' => 'Eselon III/b', 'besaran' => 35000],
            ['eselon_key' => 'III/a', 'label' => 'Eselon III/a', 'besaran' => 40000],
            ['eselon_key' => 'II/b', 'label' => 'Eselon II/b', 'besaran' => 50000],
            ['eselon_key' => 'II/a', 'label' => 'Eselon II/a', 'besaran' => 75000],
        ];

        foreach ($data as $row) {
            DB::table('ref_iuran_eselon')->updateOrInsert(
                ['eselon_key' => $row['eselon_key']],
                $row
            );
        }
    }
}

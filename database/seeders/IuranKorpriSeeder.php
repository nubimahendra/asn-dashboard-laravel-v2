<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IuranKorpriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['golongan_key' => 'I', 'label' => 'Golongan I', 'besaran' => 5000],
            ['golongan_key' => 'II', 'label' => 'Golongan II', 'besaran' => 10000],
            ['golongan_key' => 'III', 'label' => 'Golongan III', 'besaran' => 15000],
            ['golongan_key' => 'IV', 'label' => 'Golongan IV', 'besaran' => 20000],
            ['golongan_key' => 'V', 'label' => 'Golongan V', 'besaran' => 5000],
            ['golongan_key' => 'VII', 'label' => 'Golongan VII', 'besaran' => 10000],
            ['golongan_key' => 'IX', 'label' => 'Golongan IX', 'besaran' => 15000],
            ['golongan_key' => 'X', 'label' => 'Golongan X', 'besaran' => 20000],
            ['golongan_key' => 'XI', 'label' => 'Golongan XI', 'besaran' => 25000],
        ];

        foreach ($data as $item) {
            DB::table('iuran_korpri')->updateOrInsert(
                ['golongan_key' => $item['golongan_key']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}

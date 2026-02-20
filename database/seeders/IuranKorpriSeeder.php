<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IuranKorpriSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Tarif iuran KORPRI berdasarkan golongan dan ruang.
         * golongan_key = "{golongan}/{ruang}" e.g. "I/a", "IV/e"
         * label        = tampilan di UI
         * besaran      = nominal iuran (Rp), default 0, bisa diubah via UI
         *
         * Golongan PNS:
         *   I  : a, b, c, d
         *   II : a, b, c, d, e
         *   III: a, b, c, d, e
         *   IV : a, b, c, d, e
         */
        $data = [];

        $golongan = [
            'I' => ['a', 'b', 'c', 'd'],
            'II' => ['a', 'b', 'c', 'd', 'e'],
            'III' => ['a', 'b', 'c', 'd', 'e'],
            'IV' => ['a', 'b', 'c', 'd', 'e'],
        ];

        // Default besaran per golongan romawi (bisa disesuaikan)
        $defaultBesaran = [
            'I' => 5000,
            'II' => 10000,
            'III' => 15000,
            'IV' => 20000,
        ];

        foreach ($golongan as $gol => $ruangList) {
            foreach ($ruangList as $ruang) {
                $key = "{$gol}/{$ruang}";
                $data[] = [
                    'golongan_key' => $key,
                    'ruang' => $ruang,
                    'label' => "Golongan {$gol}/{$ruang}",
                    'besaran' => $defaultBesaran[$gol] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($data as $item) {
            DB::table('iuran_korpri')->updateOrInsert(
                ['golongan_key' => $item['golongan_key']],
                $item
            );
        }
    }
}

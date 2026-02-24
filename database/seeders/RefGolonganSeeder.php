<?php

namespace Database\Seeders;

use App\Models\RefGolongan;
use Illuminate\Database\Seeder;

class RefGolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $golongans = [
            // PNS
            ['id' => '11', 'nama' => 'I/a'],
            ['id' => '12', 'nama' => 'I/b'],
            ['id' => '13', 'nama' => 'I/c'],
            ['id' => '14', 'nama' => 'I/d'],
            ['id' => '21', 'nama' => 'II/a'],
            ['id' => '22', 'nama' => 'II/b'],
            ['id' => '23', 'nama' => 'II/c'],
            ['id' => '24', 'nama' => 'II/d'],
            ['id' => '31', 'nama' => 'III/a'],
            ['id' => '32', 'nama' => 'III/b'],
            ['id' => '33', 'nama' => 'III/c'],
            ['id' => '34', 'nama' => 'III/d'],
            ['id' => '41', 'nama' => 'IV/a'],
            ['id' => '42', 'nama' => 'IV/b'],
            ['id' => '43', 'nama' => 'IV/c'],
            ['id' => '44', 'nama' => 'IV/d'],
            ['id' => '45', 'nama' => 'IV/e'],

            // PPPK
            ['id' => '51', 'nama' => 'I'],
            ['id' => '52', 'nama' => 'II'],
            ['id' => '53', 'nama' => 'III'],
            ['id' => '54', 'nama' => 'IV'],
            ['id' => '55', 'nama' => 'V'],
            ['id' => '56', 'nama' => 'VI'],
            ['id' => '57', 'nama' => 'VII'],
            ['id' => '58', 'nama' => 'VIII'],
            ['id' => '59', 'nama' => 'IX'],
            ['id' => '60', 'nama' => 'X'],
            ['id' => '61', 'nama' => 'XI'],
            ['id' => '62', 'nama' => 'XII'],
            ['id' => '63', 'nama' => 'XIII'],
            ['id' => '64', 'nama' => 'XIV'],
            ['id' => '65', 'nama' => 'XV'],
            ['id' => '66', 'nama' => 'XVI'],
            ['id' => '67', 'nama' => 'XVII'],
        ];

        foreach ($golongans as $gol) {
            RefGolongan::updateOrCreate(
                ['id' => $gol['id']],
                ['nama' => $gol['nama']]
            );
        }
    }
}

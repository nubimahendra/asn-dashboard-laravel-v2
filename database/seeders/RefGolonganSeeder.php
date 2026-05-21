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

            // PPPK — prefixed with "P" to prevent ID collision with PNS
            // CSV gol_akhir_id for PPPK uses the same numeric range as PNS
            // but refers to different golongan names. Prefix prevents overwrite.
            ['id' => 'P51', 'nama' => 'I'],
            ['id' => 'P52', 'nama' => 'II'],
            ['id' => 'P53', 'nama' => 'III'],
            ['id' => 'P54', 'nama' => 'IV'],
            ['id' => 'P55', 'nama' => 'V'],
            ['id' => 'P56', 'nama' => 'VI'],
            ['id' => 'P57', 'nama' => 'VII'],
            ['id' => 'P58', 'nama' => 'VIII'],
            ['id' => 'P59', 'nama' => 'IX'],
            ['id' => 'P60', 'nama' => 'X'],
            ['id' => 'P61', 'nama' => 'XI'],
            ['id' => 'P62', 'nama' => 'XII'],
            ['id' => 'P63', 'nama' => 'XIII'],
            ['id' => 'P64', 'nama' => 'XIV'],
            ['id' => 'P65', 'nama' => 'XV'],
            ['id' => 'P66', 'nama' => 'XVI'],
            ['id' => 'P67', 'nama' => 'XVII'],
        ];

        foreach ($golongans as $gol) {
            RefGolongan::updateOrCreate(
                ['id' => $gol['id']],
                ['nama' => $gol['nama']]
            );
        }
    }
}

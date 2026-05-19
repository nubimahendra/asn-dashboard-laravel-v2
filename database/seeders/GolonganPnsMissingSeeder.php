<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GolonganPnsMissingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $golonganPns = [
            ['id' => 'PNS_IA', 'nama' => 'I/a'],
            ['id' => 'PNS_IIA', 'nama' => 'II/a'],
            ['id' => 'PNS_IIC', 'nama' => 'II/c'],
            ['id' => 'PNS_IIE', 'nama' => 'II/e'],
            ['id' => 'PNS_IIIA', 'nama' => 'III/a'],
            ['id' => 'PNS_IIIB', 'nama' => 'III/b'],
            ['id' => 'PNS_IIIC', 'nama' => 'III/c'],
            ['id' => 'PNS_IIIE', 'nama' => 'III/e'],
        ];

        // Gunakan upsert berdasarkan `nama` agar tidak duplikat jika sudah ada
        foreach ($golonganPns as $gol) {
            DB::table('ref_golongan')->updateOrInsert(
                ['nama' => $gol['nama']],
                ['id' => $gol['id'], 'nama' => $gol['nama'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}

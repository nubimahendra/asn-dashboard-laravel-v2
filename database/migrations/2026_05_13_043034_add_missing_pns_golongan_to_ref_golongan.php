<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $missing = [
            ['id' => '15', 'nama' => 'I/a'],
            ['id' => '25', 'nama' => 'II/a'],
            ['id' => '26', 'nama' => 'II/c'],
            ['id' => '27', 'nama' => 'II/e'],
            ['id' => '35', 'nama' => 'III/a'],
            ['id' => '36', 'nama' => 'III/b'],
            ['id' => '37', 'nama' => 'III/c'],
            ['id' => '38', 'nama' => 'III/e'],
        ];
        foreach ($missing as $gol) {
            DB::table('ref_golongan')->insertOrIgnore([
                'id'         => $gol['id'],
                'nama'       => $gol['nama'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('ref_golongan')->whereIn('id', ['15','25','26','27','35','36','37','38'])->delete();
    }
};

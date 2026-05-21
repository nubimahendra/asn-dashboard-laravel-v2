<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. First, seed the correct ref_golongan to ensure all 'P*' IDs exist
        Artisan::call('db:seed', [
            '--class' => 'Database\Seeders\RefGolonganSeeder'
        ]);

        // 2. Update pegawai table: update golongan_id for PPPK employees to use the 'P' prefix
        $pppkKedudukanHukum = ['71', '73', '101'];
        
        // Disable foreign key checks temporarily if needed, but since we seeded first, it should be fine.
        DB::table('pegawai')
            ->whereIn('kedudukan_hukum_id', $pppkKedudukanHukum)
            ->whereNotNull('golongan_id')
            ->where('golongan_id', 'not like', 'P%')
            ->update([
                'golongan_id' => DB::raw("CONCAT('P', golongan_id)")
            ]);
            
        // 3. Fix staging table as well
        DB::table('stg_pegawai_import')
            ->whereIn('kedudukan_hukum_id', $pppkKedudukanHukum)
            ->whereNotNull('gol_akhir_id')
            ->where('gol_akhir_id', 'not like', 'P%')
            ->update([
                'gol_akhir_id' => DB::raw("CONCAT('P', gol_akhir_id)"),
                'gol_awal_id' => DB::raw("CASE WHEN gol_awal_id IS NOT NULL THEN CONCAT('P', gol_awal_id) ELSE NULL END")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is not necessary as it's fixing bad data
    }
};

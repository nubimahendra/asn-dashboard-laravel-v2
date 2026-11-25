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
        
        // Catatan pemetaan (mengapa memakai CASE, bukan CONCAT polos):
        // Data PPPK di database asli masih memakai kode golongan gaya PNS (11-45),
        // sedangkan referensi golongan PPPK hanya menyediakan P51-P67. Karena itu
        // kode PNS dipetakan dulu ke golongan PPPK (50 + angka romawi, sub-huruf
        // dibuang) sebelum awalan 'P' ditempel. Pemetaan digabung langsung ke dalam
        // ekspresi UPDATE agar nilai yang tertulis selalu berawalan 'P' dan ada di
        // ref_golongan — tidak pernah mendaratkan nilai antara polos 51-67 yang
        // akan ditolak foreign key pegawai_golongan_id_foreign.
        // Baris yang sudah berawalan 'P' disaring oleh filter di bawah sehingga
        // operasi ini idempoten; baris non-PPPK tidak tersentuh.
        //
        // Penjaga numerik REGEXP '^[0-9]+$' wajib ada di MySQL: pada mode strict
        // (STRICT_TRANS_TABLES), CAST(nilai_non_numerik AS UNSIGNED) di dalam UPDATE
        // melempar error 1292 (bukan menghasilkan 0 seperti pada SELECT). Dengan
        // penjaga ini CAST hanya dievaluasi untuk nilai yang murni angka, sedangkan
        // nilai non-numerik dilewati apa adanya (tidak berubah, tidak error).
        // Penjaga hanya dipasang untuk driver MySQL karena SQLite tidak punya fungsi
        // REGEXP dan tidak melempar error 1292 (non-numerik ter-cast ke 0), sehingga
        // perilakunya sudah aman tanpa penjaga.
        $pakaiPenjagaNumerik = DB::connection()->getDriverName() === 'mysql';
        
        // Disable foreign key checks temporarily if needed, but since we seeded first, it should be fine.
        $updatePegawai = DB::table('pegawai')
            ->whereIn('kedudukan_hukum_id', $pppkKedudukanHukum)
            ->whereNotNull('golongan_id')
            ->where('golongan_id', 'not like', 'P%');
        if ($pakaiPenjagaNumerik) {
            $updatePegawai->where('golongan_id', 'regexp', '^[0-9]+$');
        }
        $updatePegawai->update([
            'golongan_id' => DB::raw("CONCAT('P', CASE WHEN CAST(golongan_id AS UNSIGNED) BETWEEN 11 AND 45 THEN 50 + FLOOR(CAST(golongan_id AS UNSIGNED) / 10) ELSE golongan_id END)")
        ]);
            
        // 3. Fix staging table as well
        // Pola pemetaan sama seperti langkah 2: nilai PNS 11-45 dipetakan ke 51-54
        // sebelum diberi awalan 'P', sedangkan nilai lain (termasuk yang sudah
        // berawalan 'P') dibiarkan. gol_awal_id yang NULL tetap NULL, dan nilai
        // gol_awal_id yang bukan angka murni dibiarkan apa adanya (tanpa CAST)
        // agar tidak memicu error 1292 pada mode strict.
        $penjagaAwal = $pakaiPenjagaNumerik ? " AND gol_awal_id REGEXP '^[0-9]+$'" : '';
        $updateStaging = DB::table('stg_pegawai_import')
            ->whereIn('kedudukan_hukum_id', $pppkKedudukanHukum)
            ->whereNotNull('gol_akhir_id')
            ->where('gol_akhir_id', 'not like', 'P%');
        if ($pakaiPenjagaNumerik) {
            $updateStaging->where('gol_akhir_id', 'regexp', '^[0-9]+$');
        }
        $updateStaging->update([
            'gol_akhir_id' => DB::raw("CONCAT('P', CASE WHEN CAST(gol_akhir_id AS UNSIGNED) BETWEEN 11 AND 45 THEN 50 + FLOOR(CAST(gol_akhir_id AS UNSIGNED) / 10) ELSE gol_akhir_id END)"),
            'gol_awal_id' => DB::raw("CASE WHEN gol_awal_id IS NOT NULL AND gol_awal_id NOT LIKE 'P%'{$penjagaAwal} THEN CONCAT('P', CASE WHEN CAST(gol_awal_id AS UNSIGNED) BETWEEN 11 AND 45 THEN 50 + FLOOR(CAST(gol_awal_id AS UNSIGNED) / 10) ELSE gol_awal_id END) ELSE gol_awal_id END")
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

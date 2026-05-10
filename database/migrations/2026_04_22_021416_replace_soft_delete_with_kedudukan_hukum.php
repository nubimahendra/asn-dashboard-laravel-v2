<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Insert record ref_kedudukan_hukum id="17", nama="ASN Non Aktif"
        DB::table('ref_kedudukan_hukum')->updateOrInsert(
            ['id' => '17'],
            ['nama' => 'ASN Non Aktif', 'created_at' => now(), 'updated_at' => now()]
        );

        // Step 2: Restore semua pegawai yang sudah di-soft-delete + set kedudukan_hukum_id = "17"
        DB::table('pegawai')
            ->whereNotNull('deleted_at')
            ->update([
                'kedudukan_hukum_id' => '17',
                'deleted_at' => null,
                'updated_at' => now(),
            ]);

        // Step 3: Drop kolom deleted_at
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->softDeletes();
        });

        // We could revert kedudukan_hukum_id=17 back to deleted_at=now, but skipping to avoid data loss issues if ran much later.
    }
};

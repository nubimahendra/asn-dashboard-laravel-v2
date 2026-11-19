<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('snapshot_pegawai', function (Blueprint $table) {
            $table->string('gelar_depan')->nullable()->after('nama_pegawai');
            $table->string('gelar_belakang')->nullable()->after('gelar_depan');
            $table->integer('mk_tahun')->nullable()->after('gelar_belakang');
            $table->integer('mk_bulan')->nullable()->after('mk_tahun');
            $table->string('kedudukan_hukum_id')->nullable()->after('mk_bulan');
            $table->string('kedudukan_hukum')->nullable()->after('kedudukan_hukum_id');
            $table->string('jenis_pegawai')->nullable()->after('kedudukan_hukum');
            $table->string('sumber')->nullable()->after('jenis_pegawai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snapshot_pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'gelar_depan',
                'gelar_belakang',
                'mk_tahun',
                'mk_bulan',
                'kedudukan_hukum_id',
                'kedudukan_hukum',
                'jenis_pegawai',
                'sumber',
            ]);
        });
    }
};

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
        Schema::table('history_pegawai', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable()->after('tgl_lahir');
            $table->string('jenis_kelamin')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('jenis_kelamin');
            $table->string('jenis_kawin')->nullable()->after('agama');
            $table->string('jenis_pegawai')->nullable()->after('sts_peg');

            // Kolom organisasi / pendidikan / status tambahan
            $table->string('unor_nama')->nullable()->after('golongan');
            $table->string('unor_opd')->nullable()->after('unor_nama');
            $table->string('pendidikan')->nullable()->after('unor_opd');
            $table->string('tingkat_pendidikan')->nullable()->after('pendidikan');
            $table->string('status_cpns_pns')->nullable()->after('tingkat_pendidikan');
            $table->date('tmt_cpns')->nullable()->after('status_cpns_pns');
            $table->date('tmt_pns')->nullable()->after('tmt_cpns');
            $table->string('kedudukan_hukum')->nullable()->after('tmt_pns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'jenis_kelamin',
                'agama',
                'jenis_kawin',
                'jenis_pegawai',
                'unor_nama',
                'unor_opd',
                'pendidikan',
                'tingkat_pendidikan',
                'status_cpns_pns',
                'tmt_cpns',
                'tmt_pns',
                'kedudukan_hukum'
            ]);
        });
    }
};

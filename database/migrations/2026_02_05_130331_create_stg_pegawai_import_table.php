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
        Schema::create('stg_pegawai_import', function (Blueprint $table) {
            $table->id();

            // Data dari CSV (sesuai header yang disebutkan)
            $table->string('pns_id')->nullable();
            $table->string('nip_baru')->nullable();
            $table->string('nip_lama')->nullable();
            $table->string('nama')->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();

            // Agama
            $table->string('agama_id')->nullable();
            $table->string('agama')->nullable();

            // Jenis Kawin
            $table->string('jenis_kawin_id')->nullable();
            $table->string('jenis_kawin')->nullable();

            // Jenis Pegawai
            $table->string('jenis_pegawai_id')->nullable();
            $table->string('jenis_pegawai')->nullable();

            // Kedudukan Hukum
            $table->string('kedudukan_hukum_id')->nullable();
            $table->string('kedudukan_hukum')->nullable();

            // Golongan (Awal & Akhir)
            $table->string('gol_awal_id')->nullable();
            $table->string('gol_awal')->nullable();
            $table->string('gol_akhir_id')->nullable();
            $table->string('gol_akhir')->nullable();
            $table->date('tmt_gol_akhir')->nullable();
            $table->integer('mk_tahun')->nullable();
            $table->integer('mk_bulan')->nullable();

            // Jenis Jabatan
            $table->string('jenis_jabatan_id')->nullable();
            $table->string('jenis_jabatan')->nullable();

            // Jabatan
            $table->string('jabatan_id')->nullable();
            $table->string('jabatan')->nullable();
            $table->date('tmt_jabatan')->nullable();

            // Tingkat Pendidikan
            $table->string('tingkat_pendidikan_id')->nullable();
            $table->string('tingkat_pendidikan')->nullable();

            // Pendidikan
            $table->string('pendidikan_id')->nullable();
            $table->string('pendidikan')->nullable();
            $table->integer('tahun_lulus')->nullable();

            // Unit Organisasi
            $table->string('unor_id')->nullable();
            $table->string('unor')->nullable();

            // Instansi
            $table->string('instansi_induk_id')->nullable();
            $table->string('instansi_induk')->nullable();
            $table->string('instansi_kerja_id')->nullable();
            $table->string('instansi_kerja')->nullable();

            // Lokasi Kerja
            $table->string('lokasi_kerja_id')->nullable();
            $table->string('lokasi_kerja')->nullable();

            // KPKN
            $table->string('kpkn_id')->nullable();
            $table->string('kpkn')->nullable();

            // Status ASN
            $table->string('status_cpns_pns')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();

            // Data Tambahan
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Flag IKD (dari header yang disebutkan)
            $table->string('flag_ikd')->nullable();

            // Kolom sistem untuk tracking
            $table->string('source_file')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stg_pegawai_import');
    }
};

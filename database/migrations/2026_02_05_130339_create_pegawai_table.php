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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();

            // Primary identifiers
            $table->string('pns_id')->unique();
            $table->string('nip_baru')->unique();
            $table->string('nip_lama')->nullable();

            // Personal Information
            $table->string('nama');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();

            // Contact Information
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Foreign Keys to Reference Tables
            $table->string('agama_id')->nullable();
            $table->foreign('agama_id')->references('id')->on('ref_agama')->onDelete('set null');

            $table->string('jenis_kawin_id')->nullable();
            $table->foreign('jenis_kawin_id')->references('id')->on('ref_jenis_kawin')->onDelete('set null');

            $table->string('jenis_pegawai_id')->nullable();
            $table->foreign('jenis_pegawai_id')->references('id')->on('ref_jenis_pegawai')->onDelete('set null');

            $table->string('kedudukan_hukum_id')->nullable();
            $table->foreign('kedudukan_hukum_id')->references('id')->on('ref_kedudukan_hukum')->onDelete('set null');

            // Current Status (latest from riwayat)
            $table->string('golongan_id')->nullable();
            $table->foreign('golongan_id')->references('id')->on('ref_golongan')->onDelete('set null');

            $table->string('jabatan_id')->nullable();
            $table->foreign('jabatan_id')->references('id')->on('ref_jabatan')->onDelete('set null');

            $table->string('jenis_jabatan_id')->nullable();
            $table->foreign('jenis_jabatan_id')->references('id')->on('ref_jenis_jabatan')->onDelete('set null');

            $table->string('pendidikan_id')->nullable();
            $table->foreign('pendidikan_id')->references('id')->on('ref_pendidikan')->onDelete('set null');

            $table->string('tingkat_pendidikan_id')->nullable();
            $table->foreign('tingkat_pendidikan_id')->references('id')->on('ref_tingkat_pendidikan')->onDelete('set null');

            // Organizational Structure
            $table->string('unor_id')->nullable();
            $table->foreign('unor_id')->references('id')->on('ref_unor')->onDelete('set null');

            $table->string('instansi_induk_id')->nullable();
            $table->foreign('instansi_induk_id')->references('id')->on('ref_instansi')->onDelete('set null');

            $table->string('instansi_kerja_id')->nullable();
            $table->foreign('instansi_kerja_id')->references('id')->on('ref_instansi')->onDelete('set null');

            $table->string('lokasi_kerja_id')->nullable();
            $table->foreign('lokasi_kerja_id')->references('id')->on('ref_lokasi')->onDelete('set null');

            $table->string('kpkn_id')->nullable();
            $table->foreign('kpkn_id')->references('id')->on('ref_kpkn')->onDelete('set null');

            // Status ASN
            $table->string('status_cpns_pns')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();

            // Additional flags
            $table->string('flag_ikd')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};

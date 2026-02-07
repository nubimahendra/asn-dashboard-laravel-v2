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
        // Tabel Referensi Agama
        Schema::create('ref_agama', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Jenis Kawin
        Schema::create('ref_jenis_kawin', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Jenis Pegawai
        Schema::create('ref_jenis_pegawai', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Kedudukan Hukum
        Schema::create('ref_kedudukan_hukum', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Golongan
        Schema::create('ref_golongan', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Jenis Jabatan
        Schema::create('ref_jenis_jabatan', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Jabatan
        Schema::create('ref_jabatan', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Tingkat Pendidikan
        Schema::create('ref_tingkat_pendidikan', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Pendidikan
        Schema::create('ref_pendidikan', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Unit Organisasi
        Schema::create('ref_unor', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Instansi
        Schema::create('ref_instansi', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Lokasi Kerja
        Schema::create('ref_lokasi', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi KPKN
        Schema::create('ref_kpkn', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Referensi Jenis ASN (PNS/PPPK)
        Schema::create('ref_jenis_asn', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jenis_asn');
        Schema::dropIfExists('ref_kpkn');
        Schema::dropIfExists('ref_lokasi');
        Schema::dropIfExists('ref_instansi');
        Schema::dropIfExists('ref_unor');
        Schema::dropIfExists('ref_pendidikan');
        Schema::dropIfExists('ref_tingkat_pendidikan');
        Schema::dropIfExists('ref_jabatan');
        Schema::dropIfExists('ref_jenis_jabatan');
        Schema::dropIfExists('ref_golongan');
        Schema::dropIfExists('ref_kedudukan_hukum');
        Schema::dropIfExists('ref_jenis_pegawai');
        Schema::dropIfExists('ref_jenis_kawin');
        Schema::dropIfExists('ref_agama');
    }
};

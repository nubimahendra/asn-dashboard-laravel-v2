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
        // Tabel Riwayat Status Pegawai (CPNS/PNS/PPPK)
        Schema::create('riwayat_status_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('status'); // CPNS, PNS, PPPK
            $table->date('tmt')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Riwayat Golongan
        Schema::create('riwayat_golongan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('golongan_id')->nullable();
            $table->foreign('golongan_id')->references('id')->on('ref_golongan')->onDelete('set null');
            $table->date('tmt')->nullable();
            $table->integer('mk_tahun')->nullable();
            $table->integer('mk_bulan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Riwayat Jabatan
        Schema::create('riwayat_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('jabatan_id')->nullable();
            $table->foreign('jabatan_id')->references('id')->on('ref_jabatan')->onDelete('set null');
            $table->string('jenis_jabatan_id')->nullable();
            $table->foreign('jenis_jabatan_id')->references('id')->on('ref_jenis_jabatan')->onDelete('set null');
            $table->string('unor_id')->nullable();
            $table->foreign('unor_id')->references('id')->on('ref_unor')->onDelete('set null');
            $table->date('tmt')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Riwayat Pendidikan
        Schema::create('riwayat_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('pendidikan_id')->nullable();
            $table->foreign('pendidikan_id')->references('id')->on('ref_pendidikan')->onDelete('set null');
            $table->string('tingkat_pendidikan_id')->nullable();
            $table->foreign('tingkat_pendidikan_id')->references('id')->on('ref_tingkat_pendidikan')->onDelete('set null');
            $table->integer('tahun_lulus')->nullable();
            $table->string('institusi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikan');
        Schema::dropIfExists('riwayat_jabatan');
        Schema::dropIfExists('riwayat_golongan');
        Schema::dropIfExists('riwayat_status_pegawai');
    }
};

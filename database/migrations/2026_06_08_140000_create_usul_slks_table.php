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
        Schema::create('usul_slks', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->index();
            $table->string('nama');
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('no_sk_hukdis')->nullable();
            $table->date('tmt_hukdis')->nullable();
            $table->string('no_sk_cltn')->nullable();
            $table->date('tmt_cltn')->nullable();
            $table->string('kabkota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kd_wil')->nullable();
            
            // SLKS Lama / Riwayat
            $table->string('slks_ada')->nullable();        // SLKS yang sudah diperoleh (10/20/30)
            $table->string('no_slks')->nullable();          // No Keppres SLKS lama
            $table->date('tgl_slks')->nullable();           // Tgl Keppres SLKS lama
            
            // SLKS Usulan
            $table->string('usul_slks')->nullable();        // SLKS yang diusulkan (10/20/30)
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bulan')->nullable();
            $table->string('bulanp')->nullable();           // Bulan pengusulan
            $table->string('tahunp')->nullable();           // Tahun pengusulan
            $table->string('ms_tms')->nullable();           // Memenuhi Syarat / Tidak
            $table->text('ket_tms')->nullable();            // Keterangan TMS
            
            // Status & Scalability
            $table->string('status')->default('draft_usulan');  // riwayat | draft_usulan | diajukan | disetujui | ditolak
            $table->string('jenis_pegawai')->nullable();        // PNS / PPPK
            $table->string('kedudukan_hukum_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->text('catatan')->nullable();                // Catatan tambahan
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usul_slks');
    }
};

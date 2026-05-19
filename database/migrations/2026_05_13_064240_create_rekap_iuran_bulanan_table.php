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
        Schema::create('rekap_iuran_bulanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_opd');            // Nama OPD (ref_unor.nama)
            $table->integer('bulan');              // 1-12
            $table->year('tahun');
            $table->integer('total_pegawai');
            $table->integer('total_struktural')->default(0);
            $table->integer('total_non_struktural')->default(0);
            $table->bigInteger('total_iuran')->default(0);
            $table->json('breakdown_golongan')->nullable(); // Snapshot JSON per golongan
            $table->string('created_by')->nullable();
            $table->timestamps();
            
            $table->unique(['nama_opd', 'bulan', 'tahun']); // Unique constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_iuran_bulanan');
    }
};

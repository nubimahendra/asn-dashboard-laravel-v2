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
        Schema::create('ref_jabatan_mapping', function (Blueprint $table) {
            $table->id();

            // ID Jabatan dari SIASN
            $table->string('jabatan_siasn_id', 50);

            // ID atau nama dari tabel Perbup. 
            // Kita asumsikan menunjuk ID row spesifik di ref_kelas_perbup
            $table->unsignedBigInteger('kelas_perbup_id')->nullable();

            // Aturan status validasi
            $table->enum('status_validasi', ['unvalidated', 'valid', 'invalid'])->default('unvalidated');
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('jabatan_siasn_id')
                ->references('id')
                ->on('ref_jabatan')
                ->onDelete('cascade');

            $table->foreign('kelas_perbup_id')
                ->references('id')
                ->on('ref_kelas_perbup')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jabatan_mapping');
    }
};

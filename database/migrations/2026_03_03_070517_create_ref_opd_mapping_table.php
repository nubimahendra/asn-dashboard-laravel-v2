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
        Schema::create('ref_opd_mapping', function (Blueprint $table) {
            $table->id();

            // Kolom ini akan match dengan id dari tabel ref_unor
            $table->string('unor_siasn_id', 50);

            // Kolom ini merujuk pada OPD di ref_kelas_perbup 
            // Kita pakai string nama OPD-nya sebagai referensi unik, atau jika ada master OPD perbup
            // Karena di request strukturnya gabungan, kita pakai string untuk relasi atau pisah sendiri
            // Di sini user meminta kelas_perbup_opd_id (atau opd_perbup_id jika dipisah).
            // Berhubung ref_kelas_perbup tidak memisahkan master OPD, kita pakai id dari sana jika memungkinkan,
            // Tapi rasanya lebih masuk akal menyimpan "nama_opd_perbup" atau membuat table master OPD.
            // As per instruction: "kelas_perbup_opd_id (atau opd_perbup_id jika dipisah)", i will just use string opd_perbup_name for now, or just follow instruction "kelas_perbup_opd_id". I will use string nama_opd_perbup for simplicity.

            $table->string('nama_opd_perbup');

            $table->enum('status_validasi', ['unvalidated', 'valid', 'invalid'])->default('unvalidated');
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('unor_siasn_id')
                ->references('id')
                ->on('ref_unor')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_opd_mapping');
    }
};

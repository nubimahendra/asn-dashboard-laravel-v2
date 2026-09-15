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
        Schema::create('ref_kode_wilayah', function (Blueprint $table) {
            $table->string('id', 10)->primary();   // Kode wilayah (misal: '3505', '35')
            $table->string('nama');                 // Nama wilayah
            $table->string('tipe')->nullable();     // 'provinsi' | 'kabkota' | 'kecamatan'
            $table->string('parent_id', 10)->nullable(); // Hierarki (kabkota → provinsi)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_kode_wilayah');
    }
};

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
        Schema::create('ref_kelas_perbup', function (Blueprint $table) {
            $table->id();
            $table->string('nama_opd_perbup');
            $table->string('nama_jabatan_perbup');
            $table->integer('kelas_jabatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_kelas_perbup');
    }
};

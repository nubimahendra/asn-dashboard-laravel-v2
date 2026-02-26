<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ref_iuran_korpri', function (Blueprint $table) {

            $table->id();

            $table->integer('kelas_jabatan');
            $table->decimal('nominal', 15, 2);

            $table->year('tahun_berlaku');

            $table->timestamps();

            $table->unique(['kelas_jabatan', 'tahun_berlaku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_iuran_korpri');
    }
};

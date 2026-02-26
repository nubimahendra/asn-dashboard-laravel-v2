<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iuran_korpri_transaksi', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('pegawai_id');

            $table->integer('kelas_jabatan');
            $table->decimal('nominal', 15, 2);

            $table->integer('bulan');
            $table->year('tahun');

            $table->enum('status', ['generated', 'paid', 'exempt'])
                ->default('generated');

            $table->timestamps();

            $table->unique(['pegawai_id', 'bulan', 'tahun']);

            $table->foreign('pegawai_id')
                ->references('id')
                ->on('pegawai')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_korpri_transaksi');
    }
};

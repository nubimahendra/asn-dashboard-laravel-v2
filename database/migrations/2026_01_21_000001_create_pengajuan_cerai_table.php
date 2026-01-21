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
        Schema::create('pengajuan_cerai', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->index();
            $table->string('nama');
            $table->string('jabatan');
            $table->date('tanggal_surat');
            $table->enum('jenis_pengajuan', ['Penggugat', 'Tergugat']);
            $table->string('unit_kerja'); // sub_pd
            $table->string('opd'); // pd
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cerai');
    }
};

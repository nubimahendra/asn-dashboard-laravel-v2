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
        Schema::create('ref_jabatan_kelas', function (Blueprint $table) {
            $table->id();

            $table->string('jabatan_id');
            $table->string('unor_id');

            $table->integer('kelas_jabatan');

            $table->timestamps();

            $table->foreign('jabatan_id')
                ->references('id')
                ->on('ref_jabatan')
                ->cascadeOnDelete();

            $table->foreign('unor_id')
                ->references('id')
                ->on('ref_unor')
                ->cascadeOnDelete();

            $table->unique(['jabatan_id', 'unor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jabatan_kelas');
    }
};

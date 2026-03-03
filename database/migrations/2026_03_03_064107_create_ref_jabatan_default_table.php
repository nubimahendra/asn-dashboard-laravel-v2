<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ref_jabatan_default', function (Blueprint $table) {
            $table->string('jabatan_id', 50);
            $table->integer('kelas_jabatan');
            $table->timestamps();

            // Primary key langsung jabatan_id
            $table->primary('jabatan_id');

            // Foreign key ke ref_jabatan
            $table->foreign('jabatan_id')
                ->references('id')
                ->on('ref_jabatan')
                ->onDelete('cascade');

            // Index untuk kelas (optional tapi bagus kalau sering filter)
            $table->index('kelas_jabatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_jabatan_default');
    }
};
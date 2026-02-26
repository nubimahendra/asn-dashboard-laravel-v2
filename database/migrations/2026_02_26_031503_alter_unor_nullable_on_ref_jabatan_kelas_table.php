<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ref_jabatan_kelas', function (Blueprint $table) {

            // Drop FK dulu
            $table->dropForeign(['unor_id']);
        });

        Schema::table('ref_jabatan_kelas', function (Blueprint $table) {

            // Ubah jadi nullable
            $table->string('unor_id')->nullable()->change();

            // Pasang kembali FK
            $table->foreign('unor_id')
                ->references('id')
                ->on('ref_unor')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ref_jabatan_kelas', function (Blueprint $table) {

            $table->dropForeign(['unor_id']);
        });

        Schema::table('ref_jabatan_kelas', function (Blueprint $table) {

            $table->string('unor_id')->nullable(false)->change();

            $table->foreign('unor_id')
                ->references('id')
                ->on('ref_unor')
                ->cascadeOnDelete();
        });
    }
};

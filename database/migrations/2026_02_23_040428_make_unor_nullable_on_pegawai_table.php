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
        Schema::table('pegawai', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['unor_id']);
        });

        Schema::table('pegawai', function (Blueprint $table) {
            // Ubah jadi nullable
            $table->string('unor_id')->nullable()->change();

            // Pasang kembali FK dengan nullOnDelete
            $table->foreign('unor_id')
                ->references('id')
                ->on('ref_unor')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropForeign(['unor_id']);
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('unor_id')->nullable(false)->change();
            $table->foreign('unor_id')
                ->references('id')
                ->on('ref_unor')
                ->cascadeOnDelete();
        });
    }
};

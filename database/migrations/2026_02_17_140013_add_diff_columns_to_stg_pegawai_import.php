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
        Schema::table('stg_pegawai_import', function (Blueprint $table) {
            $table->string('data_hash')->nullable();
            $table->enum('sync_status', ['new', 'changed', 'unchanged'])->nullable();
            $table->json('change_summary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stg_pegawai_import', function (Blueprint $table) {
            $table->dropColumn(['data_hash', 'sync_status', 'change_summary']);
        });
    }
};

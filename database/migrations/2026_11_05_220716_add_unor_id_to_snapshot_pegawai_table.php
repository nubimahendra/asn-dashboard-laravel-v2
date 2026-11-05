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
        Schema::table('snapshot_pegawai', function (Blueprint $table) {
            $table->string('unor_id')->nullable()->after('golongan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snapshot_pegawai', function (Blueprint $table) {
            $table->dropColumn('unor_id');
        });
    }
};

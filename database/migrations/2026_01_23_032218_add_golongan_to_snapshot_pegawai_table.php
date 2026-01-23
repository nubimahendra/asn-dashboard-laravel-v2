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
            $table->string('golongan')->nullable()->after('eselon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snapshot_pegawai', function (Blueprint $table) {
            $table->dropColumn('golongan');
        });
    }
};

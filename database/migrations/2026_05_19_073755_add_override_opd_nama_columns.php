<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('iuran_override', function (Blueprint $table) {
            $table->string('override_opd_nama')->nullable()->after('override_eselon_key');
        });

        Schema::table('iuran_override_log', function (Blueprint $table) {
            $table->string('old_opd_nama')->nullable()->after('new_eselon_key');
            $table->string('new_opd_nama')->nullable()->after('old_opd_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iuran_override', function (Blueprint $table) {
            $table->dropColumn('override_opd_nama');
        });

        Schema::table('iuran_override_log', function (Blueprint $table) {
            $table->dropColumn(['old_opd_nama', 'new_opd_nama']);
        });
    }
};

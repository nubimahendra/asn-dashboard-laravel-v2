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
        Schema::table('iuran_override_log', function (Blueprint $table) {
            $table->boolean('old_is_active')->nullable()->after('old_eselon_key');
            $table->boolean('new_is_active')->nullable()->after('new_eselon_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iuran_override_log', function (Blueprint $table) {
            $table->dropColumn(['old_is_active', 'new_is_active']);
        });
    }
};

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
        Schema::table('usul_slks', function (Blueprint $table) {
            $table->string('no_kepres')->nullable();
            $table->date('tanggal_kepres')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usul_slks', function (Blueprint $table) {
            $table->dropColumn(['no_kepres', 'tanggal_kepres']);
        });
    }
};

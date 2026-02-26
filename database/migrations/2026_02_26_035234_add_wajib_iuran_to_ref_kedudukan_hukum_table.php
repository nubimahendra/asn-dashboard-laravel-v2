<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ref_kedudukan_hukum', function (Blueprint $table) {
            $table->boolean('wajib_iuran')->default(true);
        });

        // Set PPPK Paruh Waktu -> false
        // Assuming PPPK Paruh Waktu id is '101'
        DB::table('ref_kedudukan_hukum')->where('id', '101')->update(['wajib_iuran' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_kedudukan_hukum', function (Blueprint $table) {
            //
        });
    }
};

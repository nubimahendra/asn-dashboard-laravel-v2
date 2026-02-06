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
            $table->text('import_error')->nullable()->after('source_file');
            $table->integer('row_number')->nullable()->after('import_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stg_pegawai_import', function (Blueprint $table) {
            $table->dropColumn(['import_error', 'row_number']);
        });
    }
};

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
        Schema::table('import_batches', function (Blueprint $table) {
            $table->integer('total_pegawai_before')->default(0)->after('status');
            $table->integer('total_pegawai_after')->default(0)->after('total_pegawai_before');
            $table->integer('summary_imported')->default(0)->after('total_pegawai_after');
            $table->timestamp('synced_at')->nullable()->after('summary_imported');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $table) {
            $table->dropColumn([
                'total_pegawai_before',
                'total_pegawai_after',
                'summary_imported',
                'synced_at'
            ]);
        });
    }
};

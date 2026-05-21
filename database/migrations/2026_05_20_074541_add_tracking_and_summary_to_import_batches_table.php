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
            $table->string('import_target')->nullable()->after('source_file')->comment('pns, pppk, pppkpw, or merged');
            $table->integer('processed_count')->default(0)->after('invalid_rows');
            $table->integer('error_count')->default(0)->after('processed_count');
            $table->integer('summary_new')->default(0)->after('error_count');
            $table->integer('summary_changed')->default(0)->after('summary_new');
            $table->integer('summary_unchanged')->default(0)->after('summary_changed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $table) {
            $table->dropColumn([
                'import_target',
                'processed_count',
                'error_count',
                'summary_new',
                'summary_changed',
                'summary_unchanged'
            ]);
        });
    }
};

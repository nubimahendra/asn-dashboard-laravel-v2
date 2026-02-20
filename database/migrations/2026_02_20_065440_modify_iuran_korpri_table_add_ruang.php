<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop unique constraint on golongan_key first
        Schema::table('iuran_korpri', function (Blueprint $table) {
            // Drop the unique index on golongan_key
            $table->dropUnique(['golongan_key']);
        });

        Schema::table('iuran_korpri', function (Blueprint $table) {
            // Modify golongan_key to store full value like "I/a", "II/c"
            $table->string('golongan_key', 10)->change(); // e.g. "I/a", "IV/e"
            // Add ruang column
            $table->string('ruang', 5)->nullable()->after('golongan_key'); // e.g. "a", "b", "c", "d", "e"
            // Add unique constraint on the full combination
            $table->unique(['golongan_key'], 'iuran_korpri_golongan_key_unique');
        });

        // Truncate old data - will be re-seeded
        DB::table('iuran_korpri')->truncate();
    }

    public function down(): void
    {
        Schema::table('iuran_korpri', function (Blueprint $table) {
            $table->dropUnique('iuran_korpri_golongan_key_unique');
            $table->dropColumn('ruang');
        });

        Schema::table('iuran_korpri', function (Blueprint $table) {
            $table->string('golongan_key', 10)->unique()->change();
        });
    }
};

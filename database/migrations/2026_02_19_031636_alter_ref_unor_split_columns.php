<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ref_unor', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('id');
            $table->string('nama_unit')->nullable()->after('nama_lengkap');
            $table->string('nama_opd')->nullable()->after('nama_unit');

            $table->index('nama_opd');
            $table->index('nama_unit');
        });
    }

    public function down(): void
    {
        Schema::table('ref_unor', function (Blueprint $table) {
            $table->dropIndex(['nama_opd']);
            $table->dropIndex(['nama_unit']);
            $table->dropColumn(['nama_lengkap', 'nama_unit', 'nama_opd']);
        });
    }
};
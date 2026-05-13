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
        Schema::create('iuran_override_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id');
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
            $table->string('action'); // 'create', 'update', 'delete', 'sync_reset'
            $table->string('old_golongan_key', 10)->nullable();
            $table->string('new_golongan_key', 10)->nullable();
            $table->string('old_eselon_key', 10)->nullable();
            $table->string('new_eselon_key', 10)->nullable();
            $table->string('alasan')->nullable();
            $table->string('performed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iuran_override_log');
    }
};

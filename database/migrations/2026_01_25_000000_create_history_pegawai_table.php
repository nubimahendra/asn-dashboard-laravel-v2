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
        Schema::create('history_pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip_baru')->nullable(); // Not unique here
            $table->string('nama_pegawai')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('eselon')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('pd')->nullable();
            $table->string('sub_pd')->nullable();
            $table->string('jenikel')->nullable();
            $table->string('sts_peg')->nullable();
            $table->string('tk_pend')->nullable();
            $table->string('golongan')->nullable();
            $table->string('no_hp')->nullable();
            $table->timestamp('last_sync_at')->nullable(); // From original data
            $table->timestamp('created_at')->useCurrent(); // Acts as snapshot_date
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_pegawai');
    }
};

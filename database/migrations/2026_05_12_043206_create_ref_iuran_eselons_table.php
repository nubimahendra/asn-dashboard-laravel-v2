<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_iuran_eselon', function (Blueprint $table) {
            $table->id();
            $table->string('eselon_key', 10)->unique();
            $table->string('label', 50);
            $table->integer('besaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_iuran_eselon');
    }
};

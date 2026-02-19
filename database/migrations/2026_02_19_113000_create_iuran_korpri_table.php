<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('iuran_korpri', function (Blueprint $table) {
            $table->id();
            $table->string('golongan_key', 10)->unique(); // e.g. "I", "II", "IV", "IX"
            $table->string('label', 50);                  // e.g. "Golongan I", "Golongan IV"
            $table->integer('besaran')->default(0);        // nominal iuran dalam rupiah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_korpri');
    }
};

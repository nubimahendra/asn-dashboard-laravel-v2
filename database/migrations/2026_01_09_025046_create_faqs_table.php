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
        // Drop table if exists to ensure clean state given schema change
        Schema::dropIfExists('faqs');

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question')->comment('Judul/Pertanyaan untuk referensi admin');
            $table->text('keywords')->comment('Kata kunci dipisahkan koma');
            $table->text('answer')->comment('Jawaban untuk WhatsApp');
            $table->string('category')->default('umum');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

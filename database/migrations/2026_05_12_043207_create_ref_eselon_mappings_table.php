<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_eselon_mapping', function (Blueprint $table) {
            $table->id();
            $table->string('jabatan_id')->unique(); // FK to ref_jabatan
            $table->string('eselon_key', 10);
            $table->boolean('is_auto')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_eselon_mapping');
    }
};

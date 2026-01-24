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
        Schema::create('chat_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable(); // connected, disconnected, etc.
            $table->string('quota')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('remote_id')->unique()->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('chat_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('remote_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_contacts');
        Schema::dropIfExists('chat_groups');
        Schema::dropIfExists('chat_devices');
    }
};

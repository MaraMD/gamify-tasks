<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('characters', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name', 100);
    $table->string('avatar')->nullable();
    $table->unsignedInteger('xp')->default(0); // << clave
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
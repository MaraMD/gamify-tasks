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
        Schema::create('system_events', function (Blueprint $table) {
            $table->id();
            // Usuario que generó el evento (nullable para eventos del sistema)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            // Tipo de evento (ej: 'task.created', 'auth.login')
            $table->string('type', 64);
            // Tipo de entidad relacionada (ej: 'Task', 'User')
            $table->string('entity_type', 64)->nullable();
            // ID de la entidad relacionada
            $table->unsignedBigInteger('entity_id')->nullable();
            // Mensaje descriptivo del evento
            $table->text('message')->nullable();
            // Dirección IP del usuario
            $table->string('ip', 45)->nullable();
            // User agent del navegador
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            // Índices para optimizar consultas frecuentes
            $table->index('type');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_events');
    }
};

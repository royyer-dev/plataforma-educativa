<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Asegúrate que la estructura sea 'return new class extends Migration'
return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla 'sessions' necesaria para el driver de sesión 'database'.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Clave primaria (ID de sesión)
            $table->foreignId('user_id')->nullable()->index(); // ID de usuario asociado (puede ser nulo, indexado)
            $table->string('ip_address', 45)->nullable(); // Dirección IP del usuario
            $table->text('user_agent')->nullable(); // Información del navegador/cliente
            $table->longText('payload'); // Datos de la sesión serializados (usa longText para almacenar suficiente info)
            $table->integer('last_activity')->index(); // Timestamp (entero) de la última actividad (indexado para limpieza)
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la tabla 'sessions'.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
}; // ¡No olvides el punto y coma final!
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Importar DB Facade

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modifica la columna 'estado' para añadir 'pendiente'.
     */
    public function up(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            // Modificar columna ENUM en MySQL requiere SQL crudo
            // Asegúrate que los valores existentes ('activo', 'completado', 'abandonado') estén incluidos
            // y añade el nuevo valor 'pendiente'.
            DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM('activo', 'completado', 'abandonado', 'pendiente') NOT NULL DEFAULT 'activo'");
        });
    }

    /**
     * Reverse the migrations.
     * Vuelve a quitar 'pendiente' del ENUM (revierte el cambio).
     */
    public function down(): void
    {
         Schema::table('inscripciones', function (Blueprint $table) {
            // Volver al estado anterior sin 'pendiente'
             DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM('activo', 'completado', 'abandonado') NOT NULL DEFAULT 'activo'");
        });
    }
};

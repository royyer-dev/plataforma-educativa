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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('usuarios')->onDelete('cascade'); // FK a usuarios (estudiante)
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // FK a cursos
            $table->timestamp('fecha_inscripcion')->useCurrent(); // Fecha actual por defecto
            $table->enum('estado', ['activo', 'completado', 'abandonado'])->default('activo');
            $table->timestamps();

            // Restricción única
            $table->unique(['estudiante_id', 'curso_id'], 'unique_inscripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
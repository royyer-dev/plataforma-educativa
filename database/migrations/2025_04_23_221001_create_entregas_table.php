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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade'); // FK a tareas
            $table->foreignId('estudiante_id')->constrained('usuarios')->onDelete('cascade'); // FK a usuarios (estudiante)
            $table->timestamp('fecha_entrega')->useCurrent();
            $table->string('ruta_archivo')->nullable(); // Si la entrega incluye archivo
            $table->text('texto_entrega')->nullable(); // Si la entrega es texto
            $table->string('url_entrega', 2048)->nullable(); // Si la entrega es un enlace
            $table->unsignedInteger('intento')->default(1); // Número de intento
            $table->decimal('calificacion', 8, 2)->nullable(); // Calificación con decimales
            $table->text('retroalimentacion')->nullable(); // Comentarios del profesor
            $table->timestamp('fecha_calificacion')->nullable();
            $table->foreignId('calificado_por')->nullable()->constrained('usuarios')->onDelete('set null'); // Quién calificó (FK a usuarios)
            $table->enum('estado_entrega', ['entregado', 'entregado_tarde', 'calificado', 'no_entregado'])->default('entregado');
            $table->timestamps();

            // Restricción única para evitar múltiples entregas del mismo intento
            $table->unique(['tarea_id', 'estudiante_id', 'intento'], 'unique_entrega_intento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
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
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // FK a cursos
            $table->foreignId('modulo_id')->nullable()->constrained('modulos')->onDelete('set null'); // FK opcional a modulos
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo_entrega', ['archivo', 'texto', 'url', 'ninguno'])->default('archivo');
            $table->timestamp('fecha_publicacion')->useCurrent();
            $table->timestamp('fecha_limite')->nullable();
            $table->boolean('permite_entrega_tardia')->default(false);
            $table->timestamp('fecha_limite_tardia')->nullable(); // Solo aplica si permite_entrega_tardia es true
            $table->decimal('puntos_maximos', 8, 2)->unsigned()->nullable(); // Ej: 100.00 puntos, permite decimales
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null'); // Quién la creó (FK a usuarios)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
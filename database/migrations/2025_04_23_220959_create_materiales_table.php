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
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // FK a cursos
            $table->foreignId('modulo_id')->nullable()->constrained('modulos')->onDelete('set null'); // FK opcional a modulos
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo_material', ['archivo', 'enlace', 'texto', 'video'])->default('texto');
            $table->string('ruta_archivo')->nullable(); // Si tipo_material es 'archivo'
            $table->string('enlace_url', 2048)->nullable(); // Si tipo_material es 'enlace' o 'video' (VARCHAR más largo para URLs)
            $table->text('contenido_texto')->nullable(); // Si tipo_material es 'texto'
            $table->unsignedInteger('orden')->default(0); // Para ordenar materiales
            $table->timestamp('visible_desde')->nullable(); // Para programar visibilidad
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null'); // Quién lo creó (FK a usuarios)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
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
        Schema::create('anuncios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // FK a cursos
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null'); // Quién lo creó (FK a usuarios)
            $table->string('titulo');
            $table->text('contenido');
            $table->timestamp('fecha_publicacion')->useCurrent();
            $table->boolean('es_fijo')->default(false); // Para destacar anuncios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anuncios');
    }
};
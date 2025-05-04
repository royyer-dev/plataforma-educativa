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
        Schema::create('curso_profesor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // FK a cursos
            $table->foreignId('profesor_id')->constrained('usuarios')->onDelete('cascade'); // FK a usuarios (profesor)
            $table->timestamps();

            // Restricción única
            $table->unique(['curso_id', 'profesor_id'], 'unique_curso_profesor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_profesor');
    }
};
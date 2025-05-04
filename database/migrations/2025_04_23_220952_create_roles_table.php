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
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Columna id (BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY)
            $table->string('nombre', 50)->unique(); // Columna nombre (VARCHAR(50) NOT NULL UNIQUE)
            $table->text('descripcion')->nullable(); // Columna descripcion (TEXT NULL)
            $table->timestamps(); // Columnas created_at y updated_at (TIMESTAMP NULL)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
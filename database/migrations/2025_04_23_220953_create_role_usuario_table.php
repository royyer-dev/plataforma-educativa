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
        Schema::create('role_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade'); // Clave foránea a usuarios
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Clave foránea a roles
            $table->timestamps();

            // Restricción única para evitar duplicados
            $table->unique(['usuario_id', 'role_id'], 'unique_usuario_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_usuario');
    }
};

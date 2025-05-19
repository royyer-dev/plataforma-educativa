<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Añade la columna 'genero' a la tabla 'usuarios'.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Añadir la columna 'genero' después de la columna 'telefono'
            // Puede ser nullable (opcional para el usuario)
            // Opciones para 'genero': 'masculino', 'femenino', 'otro', 'no_especificado'
            // Usaremos string por simplicidad, pero ENUM también es una opción en BD.
            $table->string('genero', 50)->nullable()->after('telefono');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la columna 'genero' de la tabla 'usuarios'.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Verificar si la columna existe antes de intentar borrarla
            if (Schema::hasColumn('usuarios', 'genero')) {
                $table->dropColumn('genero');
            }
        });
    }
};

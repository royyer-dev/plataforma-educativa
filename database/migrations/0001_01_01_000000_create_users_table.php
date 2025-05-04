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
        Schema::create('usuarios', function (Blueprint $table) { // Cambiado 'users' a 'usuarios'
            $table->id();
            $table->string('nombre', 150); // Cambiado 'name' a 'nombre' y ajustado longitud
            $table->string('apellidos', 150)->nullable(); // Nuevo campo
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telefono', 20)->nullable(); // Nuevo campo
            $table->string('ruta_foto_perfil')->nullable(); // Nuevo campo
            $table->rememberToken();
            $table->timestamps();
        });

        // Si Laravel creó estas tablas y no las necesitas ahora, puedes comentarlas o borrarlas
        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestamp('created_at')->nullable();
        // });

        // Schema::create('sessions', function (Blueprint $table) {
        //     $table->string('id')->primary();
        //     $table->foreignId('user_id')->nullable()->index();
        //     $table->string('ip_address', 45)->nullable();
        //     $table->text('user_agent')->nullable();
        //     $table->longText('payload');
        //     $table->integer('last_activity')->index();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('sessions'); // Si comentaste/borraste arriba, hazlo aquí también
        // Schema::dropIfExists('password_reset_tokens'); // Si comentaste/borraste arriba, hazlo aquí también
        Schema::dropIfExists('usuarios'); // Cambiado 'users' a 'usuarios'
    }
};

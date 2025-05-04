<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importar DB Facade
use App\Models\Role; // Importar el modelo Role

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opcional: Vaciar la tabla primero para evitar duplicados si se ejecuta varias veces
        // DB::table('roles')->truncate(); // O DB::table('roles')->delete();

        // Crear los roles esenciales
        Role::create([
            'nombre' => 'docente',
            'descripcion' => 'Permisos para Profesores / Docentes'
        ]);

        Role::create([
            'nombre' => 'estudiante',
            'descripcion' => 'Permisos para Alumnos / Estudiantes'
        ]);

        // Puedes añadir más roles aquí si los necesitas en el futuro (ej: admin)
        // Role::create(['nombre' => 'admin', 'descripcion' => 'Administrador del Sistema']);
    }
}
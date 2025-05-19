<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrera; // Importar el modelo Carrera
use Illuminate\Support\Facades\DB; // Para DB::table

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // --- vvv LÍNEA AÑADIDA/DESCOMENTADA vvv ---
        // Vaciar la tabla primero para evitar duplicados.
        // Carrera::truncate(); // Opción 1: Más rápido, pero resetea IDs y puede fallar con FKs.
        DB::table('carreras')->delete(); // Opción 2: Borra todas las filas.
        // --- ^^^ FIN LÍNEA AÑADIDA/DESCOMENTADA ^^^ -

            $carreras = [
                ['nombre' => 'Ingeniería en Sistemas Computacionales', 'descripcion' => 'Formación de profesionales en el desarrollo y gestión de sistemas de software.'],
                ['nombre' => 'Ingeniería informatica', 'descripcion' => 'Desarrollo de software y gestión de sistemas informáticos.'],
                ['nombre' => 'Ingeniería Industrial', 'descripcion' => 'Optimización de procesos productivos y de servicios.'],
                ['nombre' => 'Ingeniería Mecatrónica', 'descripcion' => 'Integración de mecánica, electrónica, informática y control.'],
                ['nombre' => 'Ingeniería en Gestión Empresarial', 'descripcion' => 'Formación para la dirección y gestión de organizaciones.'],
                ['nombre' => 'Arquitectura', 'descripcion' => 'Diseño y construcción de espacios habitables y funcionales.'],
                ['nombre' => 'Contador Público', 'descripcion' => 'Formación en contabilidad, finanzas y auditoría.'],
                ['nombre' => 'Ingenieria Ambiental', 'descripcion' => 'Estudio y gestión del medio ambiente.'],
                ['nombre' => 'Licenciatura en Administracion', 'descripcion' => 'Formación en administración y gestión de empresas.'],
                ['nombre' => 'Ingeniería Bioquímica', 'descripcion' => 'Formación en procesos bioquímicos y biotecnológicos.'],
            ];

// Insertar las carreras en la base de datos
        foreach ($carreras as $carreraData) {
            Carrera::create($carreraData);
        }
    }
}

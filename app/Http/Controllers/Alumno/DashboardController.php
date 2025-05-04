<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener el estudiante
use Illuminate\View\View;
use Carbon\Carbon; // Para fechas

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del estudiante.
     * Incluye cursos activos y próximas tareas.
     * Ruta: GET /alumno/dashboard
     * Nombre: alumno.dashboard
     */
    public function index(): View
    {
        $estudiante = Auth::user();

        // 1. Obtener Cursos Activos
        $cursosActivos = $estudiante->cursosInscritos() // Usa la relación definida en Usuario
                                  ->wherePivot('estado', 'activo') // Filtra por estado activo en la tabla inscripciones
                                  ->with('profesores') // Carga profesores opcionalmente
                                  ->orderBy('titulo')
                                  ->limit(6) // Limitar para no saturar el dashboard
                                  ->get();

        // 2. Obtener Próximas Tareas (de cursos activos)
        $cursosActivosIds = $cursosActivos->pluck('id'); // IDs de los cursos activos
        $proximasTareas = collect(); // Colección vacía por defecto

        if ($cursosActivosIds->isNotEmpty()) {
            $proximasTareas = \App\Models\Tarea::whereIn('curso_id', $cursosActivosIds)
                                ->where('fecha_limite', '>=', Carbon::now()) // Solo tareas con fecha límite futura o hoy
                                ->orderBy('fecha_limite', 'asc') // Las más próximas primero
                                ->with('curso') // Cargar el curso asociado
                                ->limit(5) // Limitar número de tareas mostradas
                                ->get();
        }


        // 3. Pasar los datos a la vista
        return view('alumno.dashboard', compact('cursosActivos', 'proximasTareas'));
    }
}

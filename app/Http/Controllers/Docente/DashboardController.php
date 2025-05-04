<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener el docente
use Illuminate\View\View;
use App\Models\Inscripcion; // Para buscar solicitudes pendientes
use App\Models\Curso; // Para los cursos

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del docente.
     * Incluye cursos impartidos y solicitudes pendientes.
     * Ruta: GET /docente/dashboard
     * Nombre: docente.dashboard
     */
    public function index(): View
    {
        $docente = Auth::user();

        // 1. Obtener Cursos que imparte el docente
        $cursosDocente = $docente->cursosImpartidos()
                                 ->orderBy('titulo')
                                 ->limit(5) // Limitar para el dashboard
                                 ->get();

        // 2. Obtener Solicitudes Pendientes para sus cursos
        $cursosIds = $docente->cursosImpartidos()->pluck('cursos.id'); // IDs de sus cursos
        $solicitudesPendientesCount = 0;
        if ($cursosIds->isNotEmpty()) {
             $solicitudesPendientesCount = Inscripcion::where('estado', 'pendiente')
                                                ->whereIn('curso_id', $cursosIds)
                                                ->count(); // Solo necesitamos el conteo para el dashboard
        }


        // 3. (Futuro) Obtener otras estadísticas o actividades recientes

        // 4. Pasar los datos a la vista
        return view('docente.dashboard', compact('cursosDocente', 'solicitudesPendientesCount'));
    }
}


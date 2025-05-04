<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para obtener estudiante
use App\Models\Entrega;              // Modelo Entrega
use Illuminate\View\View;

class CalificacionController extends Controller
{
    public function index(): View
    {
        $estudiante = Auth::user();

        // Obtener todas las entregas del estudiante que ya han sido calificadas
        $entregasCalificadas = $estudiante->entregasRealizadas() // Usa la relación del modelo Usuario
                                        ->whereNotNull('calificacion') // Solo las que tienen nota
                                        ->with([
                                            'tarea', // Cargar la tarea asociada
                                            'tarea.curso' // Cargar el curso asociado a la tarea
                                        ])
                                        ->orderBy('fecha_calificacion', 'desc') // Mostrar las más recientes primero
                                        ->get();

        $calificacionesPorCurso = $entregasCalificadas->groupBy('tarea.curso_id');

        return view('alumno.calificaciones.index', compact('calificacionesPorCurso'));
    }
}

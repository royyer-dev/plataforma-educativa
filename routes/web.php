<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
// Controladores Docente (con alias para claridad)
use App\Http\Controllers\Docente\CursoController as DocenteCursoController;
use App\Http\Controllers\Docente\ModuloController;
use App\Http\Controllers\Docente\MaterialController;
use App\Http\Controllers\Docente\TareaController as DocenteTareaController;
use App\Http\Controllers\Docente\SolicitudController;
// Controladores Alumno (con alias para claridad)
use App\Http\Controllers\Alumno\CursoController as AlumnoCursoController;
use App\Http\Controllers\Alumno\TareaController as AlumnoTareaController;
use App\Http\Controllers\Alumno\CalificacionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Alumno\DashboardController as AlumnoDashboardController;
use App\Http\Controllers\Docente\DashboardController as DocenteDashboardController;
use App\Http\Controllers\Alumno\CarreraController as AlumnoCarreraController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Autenticación ---
Auth::routes(['verify' => true]);

// --- Ruta para Notificaciones ---
Route::middleware('auth')->group(function () {
    // Mostrar historial de notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])
         ->name('notifications.index');

    // Marcar una notificación como leída y redirigir
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markAsReadAndRedirect'])
         ->name('notifications.read');

    // --- vvv RUTAS AÑADIDAS vvv ---
    // Marcar todas las notificaciones como leídas
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
         ->name('notifications.markAllRead'); // Usamos PATCH por convención

    // Eliminar una notificación específica
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
          ->name('notifications.destroy');
    });

// --- vvv AÑADIR RUTAS PARA PERFIL DE USUARIO vvv ---
Route::prefix('perfil')->name('perfil.')->group(function () {
    // Mostrar página de perfil
    Route::get('/', [PerfilController::class, 'show'])->name('show');
    // Actualizar contraseña
    Route::patch('/password', [PerfilController::class, 'updatePassword'])->name('updatePassword');
    Route::patch('/update', [PerfilController::class, 'updateProfile'])->name('update');
    Route::patch('/picture', [PerfilController::class, 'updatePicture'])->name('updatePicture');
    Route::delete('/', [PerfilController::class, 'destroy'])->name('destroy');

            // (Aquí podrías añadir rutas para actualizar nombre/email en el futuro);
    });

// --- Ruta Raíz ---
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->roles()->where('nombre', 'docente')->exists()) {
            return redirect()->route('docente.dashboard');
        } elseif ($user->roles()->where('nombre', 'estudiante')->exists()) {
            return redirect()->route('alumno.dashboard');
        } else {
            return redirect('/home');
        }
    }
    return view('welcome');
});

// --- Ruta '/home' ---
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');


// --- Grupo de Rutas para ESTUDIANTES ---
Route::middleware(['auth', 'role:estudiante'])
    ->prefix('alumno')
    ->name('alumno.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AlumnoDashboardController::class, 'index'])->name('dashboard');

        // Ver lista de todas las carreras
        Route::get('/carreras', [AlumnoCarreraController::class, 'index'])->name('carreras.index');
        
        // Ver cursos DE UNA CARRERA específica
        Route::get('/carreras/{carrera}/cursos', [AlumnoCursoController::class, 'index'])->name('cursos.index');
        
        // Solicitar inscripción a un curso
        Route::post('/cursos/{curso}/solicitar-inscripcion', [AlumnoCursoController::class, 'solicitarInscripcion'])
            ->name('cursos.solicitar-inscripcion');

        // Ver entregas en general
        //Route::get('/mis-entregas', [AlumnoDashboardController::class, 'agendaEntregas'])->name('entregas.agenda');

        // Agenda de Tareas
         Route::get('/agenda-tareas', [\App\Http\Controllers\Alumno\AgendaController::class, 'index'])->name('agenda.index');

        // Ver Detalles de un Curso específico (el acceso será desde la lista de cursos por carrera)
        Route::get('/cursos/{curso}', [AlumnoCursoController::class, 'show'])->name('cursos.show');

        // Solicitar Inscripción a Curso
        Route::post('/cursos/{curso}/solicitar', [AlumnoCursoController::class, 'solicitarInscripcion'])
             ->name('cursos.solicitar');

        // Salir de un Curso
        Route::delete('/cursos/{curso}/salir', [AlumnoCursoController::class, 'salirDelCurso'])
             ->name('cursos.salir');

        // Ver Detalles de una Tarea
        Route::get('/cursos/{curso}/tareas/{tarea}', [AlumnoTareaController::class, 'show'])
             ->name('cursos.tareas.show');

        // Guardar Entrega de Tarea
        Route::post('/cursos/{curso}/tareas/{tarea}/entregar', [AlumnoTareaController::class, 'storeEntrega'])
             ->name('cursos.tareas.storeEntrega');

        // Mis Calificaciones
        Route::get('/calificaciones', [CalificacionController::class, 'index'])
             ->name('calificaciones.index');
        // (Otras rutas de alumno...)

});



// --- Grupo de Rutas para DOCENTES ---
Route::middleware(['auth', 'role:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {

        // Dashboard (Usar solo la definición con controlador)
        Route::get('/dashboard', [DocenteDashboardController::class, 'index'])->name('dashboard');

        // Ver Cursos que imparte el docente
        Route::get('/cursos', [DocenteCursoController::class, 'index'])->name('cursos.index');

        // Ver todos los estudiantes
        Route::get('/mis-estudiantes', [DocenteDashboardController::class, 'verTodosEstudiantes'])->name('estudiantes.generales');

        Route::get('/entregas-por-calificar', [DocenteDashboardController::class, 'verEntregasPorCalificar'])->name('entregas.porCalificar');

        // CRUD Cursos
        Route::resource('cursos', DocenteCursoController::class);

        // Gestión Estudiantes
        Route::get('/cursos/{curso}/estudiantes', [DocenteCursoController::class, 'verEstudiantes'])->name('cursos.estudiantes.index');
        Route::get('/cursos/{curso}/estudiantes/{estudiante}', [DocenteCursoController::class, 'verDetallesEstudiante'])->name('cursos.estudiantes.show');
        Route::delete('/cursos/{curso}/estudiantes/{estudiante}', [DocenteCursoController::class, 'darDeBajaEstudiante'])->name('cursos.estudiantes.destroy');
        

        // CRUD Módulos
        Route::get('/cursos/{curso}/modulos/create', [ModuloController::class, 'create'])->name('cursos.modulos.create');
        Route::post('/cursos/{curso}/modulos', [ModuloController::class, 'store'])->name('cursos.modulos.store');
        Route::get('/cursos/{curso}/modulos/{modulo}/edit', [ModuloController::class, 'edit'])->name('cursos.modulos.edit');
        Route::match(['put', 'patch'], '/cursos/{curso}/modulos/{modulo}', [ModuloController::class, 'update'])->name('cursos.modulos.update');
        Route::delete('/cursos/{curso}/modulos/{modulo}', [ModuloController::class, 'destroy'])->name('cursos.modulos.destroy');

        // CRUD Materiales
        Route::get('/cursos/{curso}/materiales/create', [MaterialController::class, 'create'])->name('cursos.materiales.create');
        Route::post('/cursos/{curso}/materiales', [MaterialController::class, 'store'])->name('cursos.materiales.store');
        Route::get('/cursos/{curso}/materiales/{material}/edit', [MaterialController::class, 'edit'])->name('cursos.materiales.edit');
        Route::match(['put', 'patch'], '/cursos/{curso}/materiales/{material}', [MaterialController::class, 'update'])->name('cursos.materiales.update');
        Route::delete('/cursos/{curso}/materiales/{material}', [MaterialController::class, 'destroy'])->name('cursos.materiales.destroy');

        // CRUD Tareas
        Route::get('/cursos/{curso}/tareas/create', [DocenteTareaController::class, 'create'])->name('cursos.tareas.create');
        Route::post('/cursos/{curso}/tareas', [DocenteTareaController::class, 'store'])->name('cursos.tareas.store');
        Route::get('/cursos/{curso}/tareas/{tarea}/edit', [DocenteTareaController::class, 'edit'])->name('cursos.tareas.edit');
        Route::match(['put', 'patch'], '/cursos/{curso}/tareas/{tarea}', [DocenteTareaController::class, 'update'])->name('cursos.tareas.update');
        Route::delete('/cursos/{curso}/tareas/{tarea}', [DocenteTareaController::class, 'destroy'])->name('cursos.tareas.destroy');

        // Ver/Calificar Entregas
        Route::get('/cursos/{curso}/tareas/{tarea}/entregas', [DocenteTareaController::class, 'verEntregas'])->name('cursos.tareas.entregas.index');
        Route::get('/cursos/{curso}/tareas/{tarea}/entregas/{entrega}/calificar', [DocenteTareaController::class, 'mostrarFormularioCalificar'])->name('cursos.tareas.entregas.calificar.form');
        Route::match(['put','patch'],'/cursos/{curso}/tareas/{tarea}/entregas/{entrega}/calificar', [DocenteTareaController::class, 'guardarCalificacion'])->name('cursos.tareas.entregas.calificar.store');

        // Gestión de Solicitudes
        Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
        Route::patch('/solicitudes/{inscripcion}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
        Route::delete('/solicitudes/{inscripcion}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');

        // --- ^^^ FIN RUTAS PARA CALIFICAR ENTREGA ^^^ ---
}); // Fin del grupo de docentes


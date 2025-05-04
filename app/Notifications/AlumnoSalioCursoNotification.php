<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Curso;
use App\Models\Usuario; // El estudiante que salió

class AlumnoSalioCursoNotification extends Notification
{
    use Queueable;

    protected $curso;
    protected $estudiante;

    /**
     * Create a new notification instance.
     */
    public function __construct(Curso $curso, Usuario $estudiante)
    {
        $this->curso = $curso;
        $this->estudiante = $estudiante;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
         $studentName = $this->estudiante->nombre . ' ' . $this->estudiante->apellidos;
         $courseName = $this->curso->titulo;

        return [
            'curso_id' => $this->curso->id,
            'curso_titulo' => $courseName,
            'estudiante_id' => $this->estudiante->id,
            'estudiante_nombre' => $studentName,
            'mensaje' => 'El estudiante ' . $studentName . ' ha salido del curso "' . $courseName . '".',
            // Enlace a la lista de cursos del docente o a la gestión de estudiantes (si existiera)
            'url' => route('docente.cursos.show', $this->curso->id),
            'icono' => 'fa-user-minus',
            'tipo' => 'warning',
        ];
    }

     /**
     * Get the array representation.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

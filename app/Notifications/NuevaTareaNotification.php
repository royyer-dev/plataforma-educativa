<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Tarea;
use App\Models\Curso;

class NuevaTareaNotification extends Notification
{
    use Queueable;

    protected $tarea;
    protected $curso;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tarea $tarea)
    {
        $this->tarea = $tarea;
        $this->curso = $tarea->curso; // Asume relación tarea->curso
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
        $taskName = $this->tarea->titulo;
        $courseName = optional($this->curso)->titulo;

        return [
            'tarea_id' => $this->tarea->id,
            'tarea_titulo' => $taskName,
            'curso_id' => optional($this->curso)->id,
            'curso_titulo' => $courseName,
            'mensaje' => 'Se ha añadido una nueva tarea "' . $taskName . '" al curso "' . $courseName . '".',
            // Enlace a la página de detalles de la tarea para el alumno
            'url' => route('alumno.cursos.tareas.show', [optional($this->curso)->id, $this->tarea->id]),
            'icono' => 'fa-clipboard-list',
            'tipo' => 'primary',
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

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Entrega; // La entrega que fue calificada
use App\Models\Tarea;
use App\Models\Curso;

class TareaCalificadaNotification extends Notification
{
    use Queueable;

    protected $entrega;
    protected $tarea;
    protected $curso;

    /**
     * Create a new notification instance.
     */
    public function __construct(Entrega $entrega)
    {
        $this->entrega = $entrega;
        $this->tarea = $entrega->tarea; // Asume relación entrega->tarea
        $this->curso = $this->tarea->curso; // Asume relación tarea->curso
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Solo base de datos por ahora
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $taskName = optional($this->tarea)->titulo;
        $courseName = optional($this->curso)->titulo;
        $grade = $this->entrega->calificacion;
        $maxPoints = optional($this->tarea)->puntos_maximos ?? 'N/A';

        return [
            'entrega_id' => $this->entrega->id,
            'tarea_id' => optional($this->tarea)->id,
            'tarea_titulo' => $taskName,
            'curso_id' => optional($this->curso)->id,
            'curso_titulo' => $courseName,
            'calificacion' => $grade,
            'puntos_maximos' => $maxPoints,
            'mensaje' => 'Tu tarea "' . $taskName . '" en el curso "' . $courseName . '" ha sido calificada: ' . $grade . '/' . $maxPoints . '.',
            // Enlace a la página de detalles de la tarea para el alumno
            'url' => route('alumno.cursos.tareas.show', [optional($this->curso)->id, optional($this->tarea)->id]),
            'icono' => 'fa-check-double', // O fa-star, fa-graduation-cap
            'tipo' => 'success',
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

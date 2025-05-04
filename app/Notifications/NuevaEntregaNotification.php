<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Entrega; // La entrega que se realizó
use App\Models\Tarea;
use App\Models\Curso;
use App\Models\Usuario; // El estudiante que entregó

class NuevaEntregaNotification extends Notification
{
    use Queueable;

    protected $entrega;
    protected $estudiante;
    protected $tarea;
    protected $curso;

    /**
     * Create a new notification instance.
     */
    public function __construct(Entrega $entrega)
    {
        $this->entrega = $entrega;
        $this->estudiante = $entrega->estudiante;
        $this->tarea = $entrega->tarea;
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
        $studentName = optional($this->estudiante)->nombre . ' ' . optional($this->estudiante)->apellidos;
        $taskName = optional($this->tarea)->titulo;
        $courseName = optional($this->curso)->titulo;

        return [
            'entrega_id' => $this->entrega->id,
            'tarea_id' => optional($this->tarea)->id,
            'tarea_titulo' => $taskName,
            'curso_id' => optional($this->curso)->id,
            'curso_titulo' => $courseName,
            'estudiante_id' => optional($this->estudiante)->id,
            'estudiante_nombre' => $studentName,
            'mensaje' => $studentName . ' ha entregado la tarea "' . $taskName . '" en el curso "' . $courseName . '".',
            // Enlace a la lista de entregas de esa tarea
            'url' => route('docente.cursos.tareas.entregas.index', [optional($this->curso)->id, optional($this->tarea)->id]),
            'icono' => 'fa-file-upload',
            'tipo' => 'info',
        ];
    }

    /**
     * Get the array representation (opcional).
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

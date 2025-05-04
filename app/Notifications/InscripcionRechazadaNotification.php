<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Curso; // Importar modelo Curso
// No necesitamos Inscripcion aquí porque se borra antes de notificar

class InscripcionRechazadaNotification extends Notification
{
    use Queueable;

    protected $curso;

    /**
     * Create a new notification instance.
     * Recibe el curso al que se intentó inscribir.
     */
    public function __construct(Curso $curso) // Recibe el Curso directamente
    {
         $this->curso = $curso;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Solo base de datos por ahora
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->subject('Solicitud de Inscripción Rechazada')
    //                 ->greeting('Hola ' . $notifiable->nombre . ',')
    //                 ->line('Lamentamos informarte que tu solicitud de inscripción al curso "' . $this->curso->titulo . '" no fue aprobada en esta ocasión.')
    //                 ->line('Puedes contactar al administrador o al docente para más detalles.');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'curso_id' => $this->curso->id,
            'curso_titulo' => $this->curso->titulo,
            'mensaje' => 'Tu solicitud de inscripción al curso "' . $this->curso->titulo . '" fue rechazada.',
            'url' => route('alumno.cursos.index'), // Enlace a la lista de cursos disponibles
            'icono' => 'fa-times-circle',
            'tipo' => 'danger', // O 'warning'
        ];
    }

     /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
         return [
            'curso_id' => $this->curso->id,
            'curso_titulo' => $this->curso->titulo,
            'mensaje' => 'Tu solicitud de inscripción al curso "' . $this->curso->titulo . '" fue rechazada.',
            'url' => route('alumno.cursos.index'),
            'icono' => 'fa-times-circle',
            'tipo' => 'danger',
        ];
    }
}

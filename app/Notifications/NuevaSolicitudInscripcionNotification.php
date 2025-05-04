<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inscripcion; // Importar Inscripcion
use App\Models\Usuario; // Importar Usuario (para el estudiante)
use App\Models\Curso; // Importar Curso

class NuevaSolicitudInscripcionNotification extends Notification
{
    use Queueable;

    protected $inscripcion;
    protected $estudiante;
    protected $curso;

    /**
     * Create a new notification instance.
     * Recibe la inscripción pendiente recién creada.
     */
    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
        $this->estudiante = $inscripcion->estudiante; // Cargar estudiante
        $this->curso = $inscripcion->curso;       // Cargar curso
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Solo guardar en base de datos por ahora
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * (Opcional - Configurar si quieres email para docentes)
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     $url = route('docente.solicitudes.index'); // Enlace a la gestión de solicitudes
    //     $studentName = optional($this->estudiante)->nombre . ' ' . optional($this->estudiante)->apellidos;
    //     $courseName = optional($this->curso)->titulo;

    //     return (new MailMessage)
    //                 ->subject('Nueva Solicitud de Inscripción')
    //                 ->greeting('Hola ' . $notifiable->nombre . ',')
    //                 ->line('El estudiante ' . $studentName . ' ha solicitado inscribirse a tu curso "' . $courseName . '".')
    //                 ->action('Gestionar Solicitudes', $url)
    //                 ->line('Puedes aprobar o rechazar la solicitud desde la plataforma.');
    // }

    /**
     * Get the array/database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Asegurarse que las relaciones estén cargadas o usar optional()
        $studentName = optional($this->estudiante)->nombre . ' ' . optional($this->estudiante)->apellidos;
        $courseName = optional($this->curso)->titulo;

        return [
            'inscripcion_id' => $this->inscripcion->id,
            'curso_id' => optional($this->curso)->id,
            'curso_titulo' => $courseName,
            'estudiante_id' => optional($this->estudiante)->id,
            'estudiante_nombre' => $studentName,
            'mensaje' => $studentName . ' ha solicitado inscribirse al curso "' . $courseName . '".',
            'url' => route('docente.solicitudes.index'), // Enlace a la página de gestión
            'icono' => 'fa-user-plus', // Icono opcional
            'tipo' => 'info',
        ];
    }

     /**
     * Get the array representation (opcional si ya usas toDatabase).
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable); // Reutilizar la misma estructura
    }
}


<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inscripcion; // Importar modelo Inscripcion
use App\Models\Curso; // Importar modelo Curso

class InscripcionAprobadaNotification extends Notification // Opcional: implements ShouldQueue para enviar en segundo plano
{
    use Queueable;

    protected $inscripcion;
    protected $curso;

    /**
     * Create a new notification instance.
     * Recibe la inscripción que fue aprobada.
     */
    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
        $this->curso = $inscripcion->curso; // Acceder al curso relacionado
    }

    /**
     * Get the notification's delivery channels.
     * Define por dónde se enviará la notificación (database, mail, etc.)
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Por ahora, solo la guardaremos en la base de datos.
        // Más adelante podríamos añadir 'mail'.
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * (Opcional: Descomentar y configurar si quieres enviar email)
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     $url = route('alumno.cursos.show', $this->curso->id); // Enlace al curso

    //     return (new MailMessage)
    //                 ->subject('¡Inscripción Aprobada!')
    //                 ->greeting('¡Hola ' . $notifiable->nombre . '!')
    //                 ->line('Tu solicitud de inscripción al curso "' . $this->curso->titulo . '" ha sido aprobada.')
    //                 ->action('Ver Curso', $url)
    //                 ->line('¡Gracias por usar nuestra plataforma!');
    // }

    /**
     * Get the array representation of the notification.
     * Define qué datos se guardarán en la columna 'data' de la tabla 'notifications'.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Guardamos información útil para mostrar la notificación al usuario
        return [
            'curso_id' => $this->curso->id,
            'curso_titulo' => $this->curso->titulo,
            'mensaje' => 'Tu inscripción al curso "' . $this->curso->titulo . '" ha sido aprobada.',
            'url' => route('alumno.cursos.show', $this->curso->id), // Enlace relevante
            'icono' => 'fa-check-circle', // Icono opcional (ej: Font Awesome)
            'tipo' => 'success', // Para darle estilo en la interfaz
        ];
    }

     /**
     * Get the database representation of the notification.
     * Es una alternativa a toArray, específicamente para el canal 'database'.
     * Si defines toDatabase, Laravel lo usará en lugar de toArray para la BD.
     * (Puedes usar toArray o toDatabase, no ambos necesariamente)
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Mismos datos que en toArray, pero podemos ser más específicos si queremos
         return [
            'curso_id' => $this->curso->id,
            'curso_titulo' => $this->curso->titulo,
            'mensaje' => 'Tu inscripción al curso "' . $this->curso->titulo . '" ha sido aprobada.',
            'url' => route('alumno.cursos.show', $this->curso->id),
            'icono' => 'fa-check-circle',
            'tipo' => 'success',
        ];
    }
}

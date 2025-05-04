<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Opcionalmente: use Illuminate\Database\Eloquent\Relations\Pivot; si quieres extender la clase Pivot base
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Puedes extender Model directamente, es lo más común
class Inscripcion extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'inscripciones';

    /**
     * Indica si el modelo debe tener timestamps (created_at, updated_at).
     * La tabla 'inscripciones' sí los tiene.
     *
     * @var bool
     */
    public $timestamps = true; // Laravel lo maneja por defecto si la tabla los tiene

    /**
     * Los atributos que son asignables masivamente.
     * Incluimos las claves foráneas y el estado.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'estado',
        'fecha_inscripcion', // Aunque a menudo se establece por defecto o al crear
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inscripcion' => 'datetime',
    ];


    // --- RELACIONES ---

    /**
     * Obtiene el estudiante (Usuario) al que pertenece esta inscripción.
     */
    public function estudiante(): BelongsTo
    {
        // El segundo argumento es la clave foránea en esta tabla ('inscripciones')
        return $this->belongsTo(Usuario::class, 'estudiante_id');
    }

    /**
     * Obtiene el curso al que pertenece esta inscripción.
     */
    public function curso(): BelongsTo
    {
        // El segundo argumento es la clave foránea en esta tabla ('inscripciones')
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
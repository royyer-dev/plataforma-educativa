<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrega extends Model
{
    use HasFactory;

    protected $table = 'entregas';

    protected $fillable = [
        'tarea_id',
        'estudiante_id',
        'fecha_entrega', // Usualmente manejado por la BD o al crear el registro
        'ruta_archivo',
        'texto_entrega',
        'url_entrega',
        'intento',
        'calificacion',
        'retroalimentacion',
        'fecha_calificacion',
        'calificado_por',
        'estado_entrega',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha_entrega' => 'datetime',
        'fecha_calificacion' => 'datetime',
        'calificacion' => 'decimal:2',
    ];

    // --- RELACIONES ---

    /**
     * La tarea a la que pertenece esta Entrega.
     */
    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class);
    }

    /**
     * El estudiante que realizó esta Entrega.
     */
    public function estudiante(): BelongsTo
    {
        // Especificamos la clave foránea porque no sigue la convención 'usuario_id'
        return $this->belongsTo(Usuario::class, 'estudiante_id');
    }

    /**
     * El usuario que calificó esta Entrega (puede ser nulo).
     */
    public function calificador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'calificado_por');
    }
}
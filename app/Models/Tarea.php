<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    protected $fillable = [
        'curso_id',
        'modulo_id',
        'titulo',
        'descripcion',
        'tipo_entrega',
        'fecha_publicacion',
        'fecha_limite',
        'permite_entrega_tardia',
        'fecha_limite_tardia',
        'puntos_maximos',
        'creado_por',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_limite_tardia' => 'datetime',
        'permite_entrega_tardia' => 'boolean',
        'puntos_maximos' => 'decimal:2', // Para asegurar 2 decimales al leer/escribir
    ];

    // --- RELACIONES ---

    /**
     * El curso al que pertenece esta Tarea.
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * El módulo al que pertenece esta Tarea (puede ser nulo).
     */
    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    /**
     * El usuario que creó esta Tarea.
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    /**
     * Las entregas realizadas para esta Tarea.
     */
    public function entregas(): HasMany
    {
        // Laravel infiere 'tarea_id' como clave foránea
        return $this->hasMany(Entrega::class);
    }
}
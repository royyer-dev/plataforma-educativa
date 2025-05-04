<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';

    protected $fillable = [
        'curso_id',
        'modulo_id',
        'titulo',
        'descripcion',
        'tipo_material',
        'ruta_archivo',
        'enlace_url',
        'contenido_texto',
        'orden',
        'visible_desde',
        'creado_por',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'visible_desde' => 'datetime',
    ];

    // --- RELACIONES ---

    /**
     * El curso al que pertenece este Material.
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * El módulo al que pertenece este Material (puede ser nulo).
     */
    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    /**
     * El usuario que creó este Material.
     */
    public function creador(): BelongsTo
    {
        // Especificamos la clave foránea porque no sigue la convención 'usuario_id'
        return $this->belongsTo(Usuario::class, 'creado_por');
    }
}
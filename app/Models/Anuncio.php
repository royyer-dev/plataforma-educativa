<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anuncio extends Model
{
    use HasFactory;

    protected $table = 'anuncios';

    protected $fillable = [
        'curso_id',
        'creado_por',
        'titulo',
        'contenido',
        'fecha_publicacion', // Usualmente manejado por la BD o al crear el registro
        'es_fijo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'es_fijo' => 'boolean',
    ];

    // --- RELACIONES ---

    /**
     * El curso al que pertenece este Anuncio.
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * El usuario que creó este Anuncio.
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }
}
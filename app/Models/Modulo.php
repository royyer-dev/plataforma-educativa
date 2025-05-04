<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $fillable = [
        'curso_id',
        'titulo',
        'descripcion',
        'orden',
        'visible_desde',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'visible_desde' => 'datetime',
    ];

    // --- RELACIONES ---

    /**
     * El curso al que pertenece este Módulo.
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Los materiales que pertenecen a este Módulo.
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class)->orderBy('orden');
    }

    /**
     * Las tareas que pertenecen a este Módulo.
     */
    public function tareas(): HasMany
    {
        // Laravel infiere 'modulo_id' como clave foránea
        return $this->hasMany(Tarea::class);
    }
}
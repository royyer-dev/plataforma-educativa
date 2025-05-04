<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Define la relación uno a muchos con Cursos.
     */
    public function cursos(): HasMany
    {
        // La clave foránea en la tabla 'cursos' es 'categoria_id' por defecto
        return $this->hasMany(Curso::class);
    }
}
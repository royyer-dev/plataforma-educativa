<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// El nombre de la clase ahora es Carrera
class Carrera extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     * Ahora es 'carreras' en lugar de 'categorias'.
     *
     * @var string
     */
    protected $table = 'carreras'; // <-- Nombre de tabla actualizado

    /**
     * Los atributos que son asignables masivamente.
     * (nombre, descripcion se mantienen)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Define la relación uno a muchos con Cursos.
     * Un Carrera tiene muchos Cursos.
     */
    public function cursos(): HasMany
    {
        // La clave foránea en la tabla 'cursos' ahora será 'carrera_id'
        return $this->hasMany(Curso::class, 'carrera_id');
    }
}

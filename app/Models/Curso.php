<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Importar modelos relacionados si se usan directamente en las relaciones y no con ::class
// use App\Models\Usuario;
// use App\Models\Carrera; // <--- Importar Carrera
// use App\Models\Inscripcion;
// use App\Models\Modulo;
// use App\Models\Material;
// use App\Models\Tarea;
// use App\Models\Anuncio;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'titulo',
        'codigo_curso',
        'descripcion',
        // 'categoria_id', // <-- Eliminado
        'carrera_id',   // <-- Añadido: ahora es carrera_id
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'ruta_imagen_curso',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // --- RELACIONES ---

    /**
     * La carrera a la que pertenece el Curso.
     */
    public function carrera(): BelongsTo // <-- Renombrado de categoria() a carrera()
    {
        // Apunta al modelo Carrera y usa la clave foránea carrera_id
        // Asegúrate de que el modelo Carrera.php exista en app/Models/Carrera.php
        return $this->belongsTo(Carrera::class, 'carrera_id'); // <-- Actualizado
    }

    /**
     * Los profesores asignados a este Curso.
     */
    public function profesores(): BelongsToMany
    {
        // Modelo relacionado, tabla pivote, FK de este modelo, FK del modelo relacionado
        return $this->belongsToMany(Usuario::class, 'curso_profesor', 'curso_id', 'profesor_id')
                    ->withTimestamps(); // Si la tabla pivote tiene timestamps
    }

    /**
     * Los estudiantes inscritos en este Curso.
     * Usa la tabla 'inscripciones' como pivote.
     */
    public function estudiantes(): BelongsToMany
    {
        // Modelo relacionado, tabla pivote, FK de este modelo (Curso), FK del modelo relacionado (Usuario)
        return $this->belongsToMany(Usuario::class, 'inscripciones', 'curso_id', 'estudiante_id')
                    ->withPivot('estado', 'fecha_inscripcion') // Carga columnas extra de la tabla pivote
                    ->withTimestamps(); // Si la tabla pivote tiene timestamps
    }


    /**
     * Las inscripciones asociadas a este curso (relación directa a la tabla pivote).
     * Útil si necesitas acceder directamente a los registros de inscripción.
     */
    public function inscripciones(): HasMany
    {
        // La clave foránea en 'inscripciones' es 'curso_id'
        return $this->hasMany(Inscripcion::class); // Asume que tienes un modelo Inscripcion
    }

    /**
     * Los módulos que pertenecen a este Curso.
     */
    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class)->orderBy('orden');
    }

    /**
     * Los materiales que pertenecen a este Curso.
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Las tareas que pertenecen a este Curso.
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    /**
     * Los anuncios publicados en este Curso.
     */
    public function anuncios(): HasMany
    {
        return $this->hasMany(Anuncio::class)
                    ->orderBy('es_fijo', 'desc')
                    ->orderBy('fecha_publicacion', 'desc');
    }
}

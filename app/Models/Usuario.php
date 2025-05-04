<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <-- Asegúrate que esté
use Illuminate\Database\Eloquent\Relations\HasMany;       // <-- Asegúrate que esté
// Importar modelos relacionados si no se usa ::class
// use App\Models\Role;
// use App\Models\Curso;
// use App\Models\Inscripcion;
// use App\Models\Entrega;
// use App\Models\Material;
// use App\Models\Tarea;
// use App\Models\Anuncio;


class Usuario extends Authenticatable // implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'usuarios';

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'password',
        'telefono',
        'ruta_foto_perfil',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- RELACIONES ---

    /**
     * Los roles que pertenecen al Usuario.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_usuario', 'usuario_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Los cursos que imparte este Usuario (como profesor).
     */
    public function cursosImpartidos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'curso_profesor', 'profesor_id', 'curso_id')
                    ->withTimestamps();
    }

    /**
     * Las inscripciones directas de este Usuario (como estudiante).
     * Retorna modelos Inscripcion.
     */
    public function inscripciones(): HasMany
    {
        // La clave foránea en 'inscripciones' es 'estudiante_id'
        return $this->hasMany(Inscripcion::class, 'estudiante_id');
    }

    /**
     * Los cursos en los que está inscrito este Usuario (como estudiante).
     * Accede a los cursos a través de la tabla pivote 'inscripciones'.
     */
    public function cursosInscritos(): BelongsToMany // <-- MÉTODO AÑADIDO
    {
        // Modelo relacionado, tabla pivote, FK de este modelo (Usuario), FK del modelo relacionado (Curso)
        return $this->belongsToMany(Curso::class, 'inscripciones', 'estudiante_id', 'curso_id')
                    ->withPivot('estado', 'fecha_inscripcion') // Carga columnas extra de la tabla pivote
                    ->withTimestamps(); // Si la tabla pivote tiene timestamps
    }


    /**
     * Las entregas realizadas por este Usuario (como estudiante).
     */
    public function entregasRealizadas(): HasMany
    {
        // La clave foránea en 'entregas' es 'estudiante_id'
        return $this->hasMany(Entrega::class, 'estudiante_id');
    }

    /**
     * Las entregas calificadas por este Usuario (como profesor/calificador).
     */
    public function entregasCalificadas(): HasMany
    {
         // La clave foránea en 'entregas' es 'calificado_por'
        return $this->hasMany(Entrega::class, 'calificado_por');
    }

    /**
     * Los materiales creados por este Usuario.
     */
    public function materialesCreados(): HasMany
    {
         // La clave foránea en 'materiales' es 'creado_por'
        return $this->hasMany(Material::class, 'creado_por');
    }

    /**
     * Las tareas creadas por este Usuario.
     */
    public function tareasCreadas(): HasMany
    {
         // La clave foránea en 'tareas' es 'creado_por'
        return $this->hasMany(Tarea::class, 'creado_por');
    }

    /**
     * Los anuncios creados por este Usuario.
     */
    public function anunciosCreados(): HasMany
    {
         // La clave foránea en 'anuncios' es 'creado_por'
        return $this->hasMany(Anuncio::class, 'creado_por');
    }

    // --- Métodos Auxiliares ---

    /**
     * Verifica si el usuario tiene un rol específico.
     */
    public function tieneRole(string $nombreRole): bool
    {
        // Accede a la relación roles() y verifica si existe un rol con ese nombre
        return $this->roles()->where('nombre', $nombreRole)->exists();
    }
}

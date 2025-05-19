<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage; // Para el accesor de foto

// Importar modelos relacionados para las relaciones
use App\Models\Role;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Entrega;
use App\Models\Material;
use App\Models\Tarea;
use App\Models\Anuncio;

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
        'genero', // <-- Campo Género Añadido
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

    public function getFotoUrlAttribute(): string // El nombre del método debe ser get<NombreAtributo>Attribute
    {
        // Verifica si el usuario tiene una foto de perfil personalizada y si el archivo existe
        if ($this->ruta_foto_perfil && Storage::disk('public')->exists($this->ruta_foto_perfil)) {
            return Storage::url($this->ruta_foto_perfil); // Retorna la URL de la foto subida
        }

        // Si no hay foto personalizada, retorna una por defecto según el género
        switch (strtolower($this->genero ?? '')) { // Usar strtolower para comparar y ?? '' para evitar error si es null
            case 'femenino':
                return asset('images/default_female_avatar.png'); // Asegúrate que esta imagen exista
            case 'masculino':
                return asset('images/default_male_avatar.png');  // Asegúrate que esta imagen exista
            case 'otro':
                return asset('images/default_other_avatar.png'); // Asegúrate que esta imagen exista
            default:
                // Un avatar genérico si el género no está especificado o no coincide con los casos
                return asset('images/default_avatar.png');       // Asegúrate que esta imagen exista
        }
    }

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
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'estudiante_id');
    }

    /**
     * Los cursos en los que está inscrito este Usuario (como estudiante).
     */
    public function cursosInscritos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'inscripciones', 'estudiante_id', 'curso_id')
                    ->withPivot('estado', 'fecha_inscripcion')
                    ->withTimestamps();
    }

    /**
     * Las entregas realizadas por este Usuario (como estudiante).
     */
    public function entregasRealizadas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'estudiante_id');
    }

    /**
     * Las entregas calificadas por este Usuario (como profesor/calificador).
     */
    public function entregasCalificadas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'calificado_por');
    }

    /**
     * Los materiales creados por este Usuario.
     */
    public function materialesCreados(): HasMany
    {
        return $this->hasMany(Material::class, 'creado_por');
    }

    /**
     * Las tareas creadas por este Usuario.
     */
    public function tareasCreadas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'creado_por');
    }

    /**
     * Los anuncios creados por este Usuario.
     */
    public function anunciosCreados(): HasMany
    {
        return $this->hasMany(Anuncio::class, 'creado_por');
    }

    // --- Métodos Auxiliares ---

    /**
     * Verifica si el usuario tiene un rol específico.
     */
    public function tieneRole(string $nombreRole): bool
    {
        return $this->roles()->where('nombre', $nombreRole)->exists();
    }
}

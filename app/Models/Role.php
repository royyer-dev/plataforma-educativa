<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Define la relación muchos a muchos con Usuarios.
     */
    public function usuarios(): BelongsToMany
    {
        // Modelo relacionado, tabla pivote, FK de este modelo, FK del modelo relacionado
        return $this->belongsToMany(Usuario::class, 'role_usuario', 'role_id', 'usuario_id')
                    ->withTimestamps(); // Si la tabla pivote tiene timestamps
    }
}
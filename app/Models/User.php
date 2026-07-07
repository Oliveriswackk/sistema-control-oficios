<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Oficio;
use App\Models\Turnado;
use App\Models\OficioRelacion;
use App\Models\OficioArchivoVersion;
use App\Models\OficioHistorial;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'foto_perfil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function getFotoPerfilUrlAttribute()
    {
        if ($this->foto_perfil) {

            return asset(
                'storage/' . $this->foto_perfil
            );
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function coordinaciones()
    {
        return $this->belongsToMany(
            Coordinacion::class,
            'coordinacion_user',
            'user_id',
            'coordinacion_id'
        );
    }


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'usuario_roles',
            'usuario_id',
            'rol_id'
        )->with('permisos')->withTimestamps();
    }

    /* Permisos */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'usuario_permisos',
            'usuario_id',
            'permiso_id'
        );
    }

    
    public function hasPermission(string $permission): bool
    {
        $permisos = $this->permisos()->get();

        $porRol = $this->roles()
            ->with('permisos')
            ->get()
            ->flatMap(fn ($role) => $role->permisos);

        $directo = $permisos->contains('clave', $permission);

        $desdeRol = $porRol->contains('clave', $permission);

        return $directo || $desdeRol;
    }


    public function hasRole(string $role): bool
    {
        return $this->roles->contains('clave', $role);
    }


    public function getRolPrincipalAttribute(): ?Rol
    {
        return $this->roles()->first();
    }


    public function oficiosRegistrados(): HasMany
    {
        return $this->hasMany(Oficio::class, 'usuario_registro_id');
    }


    public function turnados(): HasMany
    {
       return $this->hasMany(Turnado::class, 'usuario_id');
    }


    public function turnadosRealizados(): HasMany
    {
        return $this->hasMany(Turnado::class, 'turnado_por_id');
    }


    public function relacionesRegistradas(): HasMany
    {
        return $this->hasMany(OficioRelacion::class, 'usuario_registro_id');
    }


    public function archivosSubidos(): HasMany
    {
        return $this->hasMany(OficioArchivoVersion::class, 'subido_por_id');
    }


    public function historial(): HasMany
    {
        return $this->hasMany(OficioHistorial::class, 'usuario_id');
    }

}
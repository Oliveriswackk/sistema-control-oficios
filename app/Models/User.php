<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'usuario_roles',
            'usuario_id',
            'rol_id'
        )->withTimestamps();
    }

    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'usuario_permisos',
            'usuario_id',
            'permiso_id'
        )->withTimestamps();
    }

    public function oficiosRegistrados(): HasMany
    {
        return $this->hasMany(Oficio::class, 'usuario_registro_id');
    }

    public function turnados(): HasMany
    {
        return $this->hasMany(Turnado::class);
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'usuario_permisos',
            'permiso_id',
            'usuario_id'
        )->withTimestamps();
    }
}
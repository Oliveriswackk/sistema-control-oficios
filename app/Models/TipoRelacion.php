<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoRelacion extends Model
{
    protected $table = 'tipos_relacion';

    protected $fillable = [
        'clave',
        'nombre',
        'direccional',
        'activo',
    ];

    protected $casts = [
        'direccional' => 'boolean',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function relaciones(): HasMany
    {
        return $this->hasMany(OficioRelacion::class, 'tipo_relacion_id');
    }
}
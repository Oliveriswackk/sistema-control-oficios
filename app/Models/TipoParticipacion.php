<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoParticipacion extends Model
{
    protected $table = 'tipos_participacion';

    protected $fillable = [
        'clave',
        'nombre',
        'implica_responsabilidad',
        'activo',
    ];

    protected $casts = [
        'implica_responsabilidad' => 'boolean',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function turnados(): HasMany
    {
        return $this->hasMany(Turnado::class);
    }
}
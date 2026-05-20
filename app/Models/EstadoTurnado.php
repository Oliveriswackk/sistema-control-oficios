<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoTurnado extends Model
{
    protected $table = 'estados_turnado';

    protected $fillable = [
        'clave',
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function turnados(): HasMany
    {
        return $this->hasMany(Turnado::class, 'estado_turnado_id');
    }
}
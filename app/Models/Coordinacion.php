<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coordinacion extends Model
{
    protected $table = 'coordinaciones';

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

    public function oficios(): HasMany
    {
        return $this->hasMany(Oficio::class, 'coordinacion_origen_id');
    }

    public function turnados(): HasMany
    {
        return $this->hasMany(Turnado::class);
    }

    public function consecutivos(): HasMany
    {
        return $this->hasMany(ConsecutivoOficio::class);
    }
}
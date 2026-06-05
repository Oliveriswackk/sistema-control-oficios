<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoOficio extends Model
{
    protected $table = 'estados_oficio';

    /*
    |--------------------------------------------------------------------------
    | CONSTANTES
    |--------------------------------------------------------------------------
    */
    public const REGISTRADO = 1;
    public const EN_SEGUIMIENTO = 2;
    public const TURNADO = 3;
    public const VENCIDO = 4;
    public const CERRADO = 5;
    public const CANCELADO = 6;

    protected $fillable = [
        'clave',
        'nombre',
        'color',
        'orden',
        'es_final',
        'activo',
    ];

    protected $casts = [
        'es_final' => 'boolean',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function oficios(): HasMany
    {
        return $this->hasMany(Oficio::class, 'estado_id');
    }

    public function historialEstadoAnterior(): HasMany
    {
        return $this->hasMany(OficioHistorial::class, 'estado_anterior_id');
    }

    public function historialEstadoNuevo(): HasMany
    {
        return $this->hasMany(OficioHistorial::class, 'estado_nuevo_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoTurnado extends Model
{
    protected $table = 'estados_turnado';

    public const ACTIVO = 'activo';
    public const EN_ATENCION = 'en_atencion';
    public const ATENDIDO = 'atendido';
    public const COMUNICADO_EXTERNAMENTE = 'comunicado_externamente';
    public const CERRADO = 'cerrado';

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
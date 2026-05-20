<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OficioHistorial extends Model
{
    protected $table = 'oficio_historial';

    protected $fillable = [
        'oficio_id',
        'usuario_id',
        'accion',
        'descripcion',
        'estado_anterior_id',
        'estado_nuevo_id',
        'entidad_relacionada',
        'entidad_relacionada_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function oficio(): BelongsTo
    {
        return $this->belongsTo(Oficio::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estadoAnterior(): BelongsTo
    {
        return $this->belongsTo(EstadoOficio::class, 'estado_anterior_id');
    }

    public function estadoNuevo(): BelongsTo
    {
        return $this->belongsTo(EstadoOficio::class, 'estado_nuevo_id');
    }
}
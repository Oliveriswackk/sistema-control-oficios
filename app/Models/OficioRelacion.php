<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OficioRelacion extends Model
{
    protected $table = 'oficio_relaciones';

    protected $fillable = [
        'oficio_origen_id',
        'oficio_relacionado_id',
        'tipo_relacion_id',
        'usuario_registro_id',
    ];
}
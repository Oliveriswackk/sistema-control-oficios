<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turnado extends Model
{
    protected $table = 'turnados';

    protected $fillable = [
        'oficio_id',
        'usuario_id',
        'coordinacion_id',
        'tipo_participacion_id',
        'estado_turnado_id',
        'turnado_por_id',
        'turnado_en',
        'atendido_en',
        'cerrado_en',
        'es_principal',
        'observaciones',
    ];

    protected $casts = [
        'turnado_en' => 'datetime',
        'atendido_en' => 'datetime',
        'cerrado_en' => 'datetime',

        'es_principal' => 'boolean',
    ];
}
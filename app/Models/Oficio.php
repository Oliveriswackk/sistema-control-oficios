<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Oficio extends Model
{
    use HasUuids;

    protected $table = 'oficios';

    protected $fillable = [
        'uuid',
        'numero_oficio',
        'tipo_oficio_id',
        'estado_id',
        'asunto',
        'descripcion',
        'fecha_oficio',
        'fecha_recepcion',
        'fecha_limite',
        'requiere_respuesta',
        'es_sensible',
        'destinatario_principal_id',
        'coordinacion_origen_id',
        'usuario_registro_id',
        'respondido_en',
        'cerrado_en',
        'cancelado_en',
    ];

    protected $casts = [
        'fecha_oficio' => 'date',
        'fecha_recepcion' => 'date',
        'fecha_limite' => 'date',

        'requiere_respuesta' => 'boolean',
        'es_sensible' => 'boolean',

        'respondido_en' => 'datetime',
        'cerrado_en' => 'datetime',
        'cancelado_en' => 'datetime',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// TODO:
// Revisar cálculo automático del estado general del oficio.
// Actualmente se marca como turnado al crear el primer turnado.


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

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function oficio()
    {
        return $this->belongsTo(Oficio::class);
    }


    public function usuario()
    {
        return $this->belongsTo(User::class);
    }


    public function coordinacion()
    {
        return $this->belongsTo(Coordinacion::class);
    }


    public function tipoParticipacion()
    {
        return $this->belongsTo(TipoParticipacion::class);
    }


    public function estadoTurnado()
    {
        return $this->belongsTo(EstadoTurnado::class);
    }


    public function turnadoPor()
    {
        return $this->belongsTo(User::class, 'turnado_por_id');
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(function ($turnado) {

            $oficio = $turnado->oficio;

            // 1. actualizar estado del oficio
            $oficio->estado_id = 3; // "turnado" (confirmado en tu seed)
            $oficio->save();

            // 2. registrar historial mínimo
            \DB::table('oficio_historial')->insert([
                'oficio_id' => $oficio->id,
                'usuario_id' => $turnado->turnado_por_id,
                'accion' => 'turnado_creado',
                'descripcion' => 'Se generó turnado operativo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
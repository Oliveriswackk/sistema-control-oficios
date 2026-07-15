<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\TipoOficio;
use App\Models\EstadoOficio;
use App\Models\Coordinacion;
use App\Models\User;
use App\Models\Turnado;
use App\Models\Tag;
use App\Models\OficioArchivo;
use App\Models\OficioHistorial;
use App\Models\OficioRelacion;

class Oficio extends Model
{
    use HasFactory;

    protected $table = 'oficios';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'uuid',

        'numero_oficio',
        'consecutivo',

        'tipo_oficio_id',
        'estado_id',

        'asunto',
        'descripcion',

        'fecha_oficio',
        'fecha_recepcion',
        'fecha_limite',

        'requiere_respuesta',
        'respuesta_a_oficio_id',
        'es_sensible',

        /*
        |--------------------------------------------------------------------------
        | Remitente
        |--------------------------------------------------------------------------
        */

        'remitente_nombre',
        'remitente_cargo',
        'remitente_dependencia',

        /*
        |--------------------------------------------------------------------------
        | Destinatario
        |--------------------------------------------------------------------------
        */

        'destinatario_nombre',
        'destinatario_cargo',
        'destinatario_dependencia',

        /*
        |--------------------------------------------------------------------------
        | Quien elabora
        |--------------------------------------------------------------------------
        */

        'quien_elabora_nombre',
        'quien_elabora_cargo',

        /*
        |--------------------------------------------------------------------------
        | Internos
        |--------------------------------------------------------------------------
        */

        'responsable_inicial_id',

        'coordinacion_origen_id',

        'usuario_registro_id',

        /*
        |--------------------------------------------------------------------------
        | Referencias
        |--------------------------------------------------------------------------
        */

        'link_documento',
        'estado_actual',

        /*
        |--------------------------------------------------------------------------
        | Flujo
        |--------------------------------------------------------------------------
        */

        'respondido_en',
        'cerrado_en',
        'cancelado_en',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | UBICAR
    |--------------------------------------------------------------------------
    */

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


    
    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(
            TipoOficio::class,
            'tipo_oficio_id'
        );
    }


    public function estado(): BelongsTo
    {
        return $this->belongsTo(
            EstadoOficio::class,
            'estado_id'
        );
    }

    
    public function coordinacionOrigen(): BelongsTo
    {
        return $this->belongsTo(
            Coordinacion::class,
            'coordinacion_origen_id'
        );
    }


    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_registro_id'
        );
    }


    public function responsableInicial(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'responsable_inicial_id'
        );
    }


    public function turnados(): HasMany
    {
        return $this->hasMany(
            Turnado::class,
            'oficio_id'
        );
    }


    public function responsableActual()
    {
        return $this->hasOne(
            Turnado::class,
            'oficio_id'
        )
        ->where('tipo_participacion_id', 1)
        ->latest('id');
    }


    public function archivos(): HasMany
    {
        return $this->hasMany(
            OficioArchivo::class,
            'oficio_id'
        );
    }


    public function historial(): HasMany
    {
        return $this->hasMany(
            OficioHistorial::class,
            'oficio_id'
        );
    }


    public function relacionesOrigen(): HasMany
    {
        return $this->hasMany(
            OficioRelacion::class,
            'oficio_origen_id'
        );
    }


    public function relacionesRelacionadas(): HasMany
    {
        return $this->hasMany(
            OficioRelacion::class,
            'oficio_relacionado_id'
        );
    }
    

    /*
    |--------------------------------------------------------------------------
    | TRAZABILIDAD
    |--------------------------------------------------------------------------
    */
    public function registrarEvento(
        string $accion,
        ?string $descripcion = null,
        ?int $estadoAnterior = null,
        ?int $estadoNuevo = null,
        ?string $entidad = null,
        ?int $entidadId = null
    ) {
        return $this->historial()->create([
            'usuario_id' => auth()->id(),
            'accion' => $accion,
            'descripcion' => $descripcion,
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => $estadoNuevo,
            'entidad_relacionada' => $entidad,
            'entidad_relacionada_id' => $entidadId,
        ]);
    }


    public function oficioPadre()
    {
        return $this->belongsTo(
            Oficio::class,
            'respuesta_a_oficio_id'
        );
    }


    public function respuestas()
    {
        return $this->hasMany(
            Oficio::class,
            'respuesta_a_oficio_id'
        );
    }

}
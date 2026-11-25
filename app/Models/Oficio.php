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
    public const ACCION_RELACION_NUEVA_CONFIRMADA =
        'relacion_nueva_confirmada';

    public const ACCION_MARCADO_COMO_ENVIADO =
        'oficio_marcado_como_enviado';

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
        'link_drive',
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
        ->whereHas('tipoParticipacion', function ($query) {
            $query->where('implica_responsabilidad', true);
        })
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

    public function relacionDefinida(): bool
    {
        if (!empty($this->respuesta_a_oficio_id)) {
            return (int) $this->respuesta_a_oficio_id !== (int) $this->getKey();
        }

        return $this->historial()
            ->where(
                'accion',
                self::ACCION_RELACION_NUEVA_CONFIRMADA
            )
            ->exists();
    }

    public function confirmarRelacionNueva(): void
    {
        if ($this->relacionDefinida()) {
            return;
        }

        $this->registrarEvento(
            self::ACCION_RELACION_NUEVA_CONFIRMADA,
            'Se confirmó que el oficio inicia una nueva petición.'
        );
    }

    public function yaFueMarcadoComoEnviado(): bool
    {
        $marcadoCount = $this->getAttribute(
            'marcado_como_enviado_count'
        );

        if ($marcadoCount !== null) {
            return (int) $marcadoCount > 0;
        }

        return $this->historial()
            ->where('accion', self::ACCION_MARCADO_COMO_ENVIADO)
            ->exists();
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
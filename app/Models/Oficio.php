<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\TipoOficio;
use App\Models\EstadoOficio;
use App\Models\Coordinacion;
use App\Models\User;
use App\Models\Turnado;
use App\Models\OficioArchivo;
use App\Models\OficioHistorial;
use App\Models\OficioRelacion;

class Oficio extends Model
{
    use HasUuids;

    protected $table = 'oficios';

    /*
    |--------------------------------------------------------------------------
    | CONFIG UUID / PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    public $incrementing = false;
    protected $keyType = 'string';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

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
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoOficio::class, 'tipo_oficio_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoOficio::class, 'estado_id');
    }

    public function coordinacionOrigen(): BelongsTo
    {
        return $this->belongsTo(Coordinacion::class, 'coordinacion_origen_id');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    public function turnados(): HasMany
    {
        return $this->hasMany(Turnado::class, 'oficio_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(OficioArchivo::class, 'oficio_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(OficioHistorial::class, 'oficio_id');
    }

    public function relacionesOrigen(): HasMany
    {
        return $this->hasMany(OficioRelacion::class, 'oficio_origen_id');
    }

    public function relacionesRelacionadas(): HasMany
    {
        return $this->hasMany(OficioRelacion::class, 'oficio_relacionado_id');
    }
}
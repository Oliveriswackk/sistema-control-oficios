<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OficioArchivoVersion extends Model
{
    protected $table = 'oficio_archivo_versiones';

    protected $fillable = [
        'oficio_archivo_id',
        'ruta',
        'mime_type',
        'tamano',
        'hash_sha256',
        'version',
        'es_actual',
        'es_publica',
        'subido_por_id',
        'motivo_reemplazo',
        'version_anterior_id',
    ];

    protected $casts = [
        'es_actual' => 'boolean',
        'es_publica' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function archivo()
    {
        return $this->belongsTo(OficioArchivo::class, 'oficio_archivo_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por_id');
    }

    public function versionAnterior()
    {
        return $this->belongsTo(
            OficioArchivoVersion::class,
            'version_anterior_id'
        );
    }

    public function versionesDerivadas()
    {
        return $this->hasMany(
            OficioArchivoVersion::class,
            'version_anterior_id'
        );
    }
}
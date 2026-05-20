<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OficioArchivo extends Model
{
    protected $table = 'oficio_archivos';

    protected $fillable = [
        'oficio_id',
        'nombre_original',
        'tipo_archivo',
        'nivel_acceso',
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

    public function versiones()
    {
        return $this->hasMany(OficioArchivoVersion::class);
    }
}
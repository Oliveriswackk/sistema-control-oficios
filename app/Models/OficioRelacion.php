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

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function oficioOrigen()
    {
        return $this->belongsTo(Oficio::class, 'oficio_origen_id');
    }

    public function oficioRelacionado()
    {
        return $this->belongsTo(Oficio::class, 'oficio_relacionado_id');
    }

    public function tipoRelacion()
    {
        return $this->belongsTo(TipoRelacion::class);
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }
}
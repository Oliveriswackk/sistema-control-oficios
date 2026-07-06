<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolioReservado extends Model
{
    protected $table = 'folios_reservados';

    protected $fillable = [
        'coordinacion_id',
        'anio',
        'numero',
        'numero_oficio',
        'estado',
        'grupo_uuid',
        'usuario_reserva_id',
        'motivo_cancelacion',
    ];

    public function coordinacion()
    {
        return $this->belongsTo(Coordinacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_reserva_id');
    }
}
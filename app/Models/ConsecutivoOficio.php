<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsecutivoOficio extends Model
{
    protected $table = 'consecutivos_oficio';

    protected $fillable = [
        'coordinacion_id',
        'anio',
        'ultimo_numero',
    ];
}

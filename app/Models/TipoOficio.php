<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoOficio extends Model
{
    protected $table = 'tipos_oficio';

    protected $fillable = [
        'clave',
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function oficios(): HasMany
    {
        return $this->hasMany(Oficio::class, 'tipo_oficio_id');
    }
}
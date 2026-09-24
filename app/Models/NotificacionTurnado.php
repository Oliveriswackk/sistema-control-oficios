<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacionTurnado extends Model
{
    protected $table = 'notificaciones_turnado';

    protected $fillable = [
        'turnado_id',
        'destinatario_email',
        'estado',
        'message_id',
        'intentos',
        'ultimo_intento_en',
        'enviado_en',
        'no_entregado_en',
        'notificado_manualmente_en',
        'notificado_manualmente_por_id',
        'ultimo_error',
    ];

    protected $casts = [
        'ultimo_intento_en' => 'datetime',
        'enviado_en' => 'datetime',
        'no_entregado_en' => 'datetime',
        'notificado_manualmente_en' => 'datetime',
    ];

    public const ESTADO_EXITOSO = 'EXITOSO';

    public const ESTADO_FALLIDO = 'FALLIDO';

    public const ESTADO_NO_ENTREGADO = 'NO_ENTREGADO';

    public const ESTADO_ENVIADO_MANUAL = 'ENVIADO_MANUAL';

    public function turnado(): BelongsTo
    {
        return $this->belongsTo(
            Turnado::class,
            'turnado_id'
        );
    }

    public function notificadoManualmentePor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'notificado_manualmente_por_id'
        );
    }
}
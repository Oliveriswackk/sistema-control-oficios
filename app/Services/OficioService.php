<?php

namespace App\Services;

use App\Models\Oficio;
use App\Models\Coordinacion;
use App\Models\ConsecutivoOficio;
use Illuminate\Support\Facades\DB;

class OficioService
{
    public function generarNumeroOficio(int $coordinacionId, string $fecha)
    : array {

    return DB::transaction(function () use (
        $coordinacionId,
        $fecha
    ) {

        $anio = date('Y', strtotime($fecha));

        $coordinacion = Coordinacion::findOrFail($coordinacionId);

        $consecutivo = ConsecutivoOficio::where(
                'coordinacion_id',
                $coordinacionId
            )
            ->where('anio', $anio)
            ->lockForUpdate()
            ->first();

        if (!$consecutivo) {

            $consecutivo = ConsecutivoOficio::create([
                'coordinacion_id' => $coordinacionId,
                'anio' => $anio,
                'ultimo_numero' => 1,
            ]);

            $numero = 1;

        } else {

            $consecutivo->increment('ultimo_numero');

            $consecutivo->refresh();

            $numero = $consecutivo->ultimo_numero;

        }

        return [

            'numero_oficio' =>
                "SESEA-{$coordinacion->clave}-" .
                str_pad($numero, 3, '0', STR_PAD_LEFT) .
                "-{$anio}",

            'consecutivo' => $numero,

        ];

    });

}
}
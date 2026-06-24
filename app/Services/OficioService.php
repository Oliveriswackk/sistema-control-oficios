<?php

namespace App\Services;

use App\Models\Oficio;
use App\Models\Coordinacion;

class OficioService
{
    public function generarNumeroOficio(int $coordinacionId, string $fecha): array
    {
        $anio = date('Y', strtotime($fecha));

        $coordinacion = Coordinacion::findOrFail($coordinacionId);

        $clave = $coordinacion->clave; // ST, CA, etc

        // buscar último consecutivo del año y coordinación
        $ultimo = Oficio::where('coordinacion_origen_id', $coordinacionId)
            ->whereYear('fecha_oficio', $anio)
            ->orderBy('id', 'desc')
            ->first();

        $nuevoConsecutivo = 1;

        if ($ultimo && $ultimo->consecutivo) {
            $nuevoConsecutivo = ((int) $ultimo->consecutivo) + 1;
        }

        $consecutivoFormateado = str_pad($nuevoConsecutivo, 3, '0', STR_PAD_LEFT);

        $numeroOficio = "SESEA-{$clave}-{$consecutivoFormateado}-{$anio}";

        return [
            'numero_oficio' => $numeroOficio,
            'consecutivo' => $consecutivoFormateado,
        ];
    }
}
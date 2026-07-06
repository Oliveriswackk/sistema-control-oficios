<?php

namespace App\Services;

use App\Models\Coordinacion;
use App\Models\ConsecutivoOficio;
use App\Models\FolioReservado;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OficioService
{
    /*
    Solo consulta cuál sería el siguiente número
    NO modifica la base de datos
    */
    public function obtenerSiguienteNumero(int $coordinacionId, string $fecha): array 
    {

        $anio = date('Y', strtotime($fecha));

        $coordinacion = Coordinacion::findOrFail($coordinacionId);

        $ultimo = ConsecutivoOficio::where(
                'coordinacion_id',
                $coordinacionId
            )
            ->where('anio', $anio)
            ->value('ultimo_numero');

        $numero = ($ultimo ?? 0) + 1;

        return [

            'numero_oficio' =>
                "SESEA-{$coordinacion->clave}-" .
                str_pad($numero, 3, '0', STR_PAD_LEFT) .
                "-{$anio}",

            'consecutivo' => $numero,

        ];
    }


    /*
    Consume definitivamente el consecutivo
    ESTE SÍ escribe en BD
    */
    public function consumirSiguienteNumero(int $coordinacionId, string $fecha): array 
    {

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

            } else {

                $consecutivo->increment('ultimo_numero');
                $consecutivo->refresh();

            }

            return [

                'numero_oficio' =>
                    "SESEA-{$coordinacion->clave}-" .
                    str_pad(
                        $consecutivo->ultimo_numero,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ) .
                    "-{$anio}",

                'consecutivo' =>
                    $consecutivo->ultimo_numero,

            ];

        });

    }


    // Reservar No. Oficio
    public function reservarNumeros(int $coordinacionId, string $fecha, int $cantidad): array 
    {
        return DB::transaction(function () use ($coordinacionId, $fecha, $cantidad) {

            $anio = date('Y', strtotime($fecha));

            $coordinacion = Coordinacion::findOrFail($coordinacionId);

            $consecutivo = ConsecutivoOficio::where('coordinacion_id', $coordinacionId)
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();

            $ultimo = $consecutivo?->ultimo_numero ?? 0;

            if (!$consecutivo) {

                $consecutivo = ConsecutivoOficio::create([
                    'coordinacion_id' => $coordinacionId,
                    'anio' => $anio,
                    'ultimo_numero' => $cantidad,
                ]);

            } else {

                $consecutivo->increment('ultimo_numero', $cantidad);
                $consecutivo->refresh();
            }

            $grupoUuid = (string) Str::uuid();

            $folios = [];

            for ($i = 1; $i <= $cantidad; $i++) {

                $numero = $ultimo + $i;

                $folios[] = FolioReservado::create([
                    'coordinacion_id' => $coordinacionId,
                    'anio' => $anio,
                    'numero' => $numero,
                    'estado' => 'reservado',
                    'grupo_uuid' => $grupoUuid,
                    'usuario_reserva_id' => auth()->id(),
                ]);
            }

            return [
                'grupo_uuid' => $grupoUuid,
                'cantidad' => $cantidad,
                'folios' => $folios,
            ];
        });
    }


}
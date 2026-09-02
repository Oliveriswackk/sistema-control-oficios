<?php

namespace App\Services;

use App\Models\Coordinacion;
use App\Models\ConsecutivoOficio;
use App\Models\FolioReservado;
use App\Models\Oficio;
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

        /*
        |--------------------------------------------------------------------------
        | Último consecutivo registrado en el control
        |--------------------------------------------------------------------------
        */

        $consecutivo = ConsecutivoOficio::where(
                'coordinacion_id',
                $coordinacionId
            )
            ->where('anio', $anio)
            ->first();

        $ultimoControl =
            $consecutivo?->ultimo_numero ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Último consecutivo realmente existente en OFICIOS
        |--------------------------------------------------------------------------
        |
        | Esto permite reconocer registros importados/manualmente
        | que todavía no estén reflejados en ConsecutivoOficio.
        |
        */

        $ultimoOficio = Oficio::where(
                'coordinacion_origen_id',
                $coordinacionId
            )
            ->where('tipo_oficio_id', 1)
            ->whereYear('fecha_oficio', $anio)
            ->max('consecutivo');


        /*
        |--------------------------------------------------------------------------
        | Tomamos el mayor de ambas fuentes
        |--------------------------------------------------------------------------
        */

        $ultimo = max(
            $ultimoControl,
            $ultimoOficio ?? 0
        );

        $numero = $ultimo + 1;


        /*
        |--------------------------------------------------------------------------
        | Consecutivo automático
        |--------------------------------------------------------------------------
        */

        $consecutivoAutomatico = [

            'numero_oficio' =>
                "SESEA-{$coordinacion->clave}-" .
                str_pad($numero, 3, '0', STR_PAD_LEFT) .
                "-{$anio}",

            'consecutivo' => $numero,

        ];


        /*
        |--------------------------------------------------------------------------
        | Folios reservados disponibles
        |--------------------------------------------------------------------------
        */

        $reservados = FolioReservado::where(
                'coordinacion_id',
                $coordinacionId
            )
            ->where('anio', $anio)
            ->where('estado', 'reservado')
            ->orderBy('numero')
            ->get()
            ->map(function ($folio) use ($coordinacion, $anio) {

                return [

                    'id' => $folio->id,

                    'numero' => $folio->numero,

                    'numero_oficio' =>
                        "SESEA-{$coordinacion->clave}-" .
                        str_pad($folio->numero, 3, '0', STR_PAD_LEFT) .
                        "-{$anio}",

                ];

            })
            ->values();


        return [

            'consecutivo' => $consecutivoAutomatico,

            'reservados' => $reservados,

            'cantidad_reservados' => $reservados->count(),

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


            /*
            |--------------------------------------------------------------------------
            | Bloqueamos el control de consecutivos
            |--------------------------------------------------------------------------
            */

            $consecutivo = ConsecutivoOficio::where(
                    'coordinacion_id',
                    $coordinacionId
                )
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();


            $ultimoControl =
                $consecutivo?->ultimo_numero ?? 0;


            /*
            |--------------------------------------------------------------------------
            | Revisamos también los oficios existentes
            |--------------------------------------------------------------------------
            */

            $ultimoOficio = Oficio::where(
                    'coordinacion_origen_id',
                    $coordinacionId
                )
                ->where('tipo_oficio_id', 1)
                ->whereYear('fecha_oficio', $anio)
                ->max('consecutivo');


            /*
            |--------------------------------------------------------------------------
            | El siguiente debe partir del mayor existente
            |--------------------------------------------------------------------------
            */

            $ultimo = max(
                $ultimoControl,
                $ultimoOficio ?? 0
            );

            $numero = $ultimo + 1;


            /*
            |--------------------------------------------------------------------------
            | Actualizamos el control
            |--------------------------------------------------------------------------
            */

            if (!$consecutivo) {

                $consecutivo = ConsecutivoOficio::create([
                    'coordinacion_id' => $coordinacionId,
                    'anio' => $anio,
                    'ultimo_numero' => $numero,
                ]);

            } else {

                $consecutivo->update([
                    'ultimo_numero' => $numero,
                ]);

            }


            return [

                'numero_oficio' =>
                    "SESEA-{$coordinacion->clave}-" .
                    str_pad(
                        $numero,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ) .
                    "-{$anio}",

                'consecutivo' => $numero,

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
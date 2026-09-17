<?php

namespace App\Console\Commands;

use App\Services\Historico\AsociarOficiosPdfService;
use App\Services\Historico\ImportarOficiosService;
use Illuminate\Console\Command;

class MigrarHistorico extends Command
{
    protected $signature =
        'historico:migrar {ruta}';

    protected $description =
        'Importa oficios históricos y asocia sus PDFs';

    public function handle(
        ImportarOficiosService $importador,
        AsociarOficiosPdfService $asociador
    ): int {
        $ruta = rtrim(
            $this->argument('ruta'),
            DIRECTORY_SEPARATOR
        );

        $rutaOficios =
            $ruta .
            DIRECTORY_SEPARATOR .
            'Oficios';

        try {
            $this->mostrarInicio(
                $ruta
            );

            $this->newLine();

            $this->info(
                '1. Importando oficios...'
            );

            $importacion =
                $importador->ejecutar(
                    $rutaOficios
                );

            $this->mostrarImportacion(
                $importacion
            );

            $this->newLine();

            $this->info(
                '2. Asociando PDFs...'
            );

            $numerosDescartados =
                array_map(
                    fn ($registro) =>
                        $registro['numero'],
                    $importacion[
                        'descartados_con_numero'
                    ]
                );

            $pdfs =
                $asociador->ejecutar(
                    $ruta,
                    $numerosDescartados,
                    $importacion[
                        'oficios_objetivo'
                    ]
                );

            $this->mostrarPdf(
                $pdfs
            );

            $this->mostrarFinal(
                $importacion,
                $pdfs
            );

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->newLine();

            $this->error(
                'La migración histórica fue cancelada.'
            );

            $this->error(
                $e->getMessage()
            );

            return Command::FAILURE;
        }
    }

    private function mostrarInicio(
        string $ruta
    ): void {
        $this->info(
            '======================================'
        );

        $this->info(
            '       MIGRACIÓN HISTÓRICA'
        );

        $this->info(
            '======================================'
        );

        $this->line(
            "Carpeta: {$ruta}"
        );
    }

    private function mostrarImportacion(
        array $resultado
    ): void {
        foreach (
            $resultado['estadisticas']
            as $tipo => $datos
        ) {
            $this->newLine();

            $this->line($tipo);

            $this->line(
                "  Leídos      : {$datos['leidos']}"
            );

            $this->line(
                "  Importados  : {$datos['importados']}"
            );

            $this->line(
                "  Duplicados  : {$datos['duplicados']}"
            );

            $this->line(
                "  Descartados : {$datos['descartados']}"
            );
        }

        if (!empty($resultado['reservados'])) {
            $this->newLine();

            $this->info(
                'Folios reservados: ' .
                count($resultado['reservados'])
            );
        }

        if (
            !empty(
                $resultado['descartados_con_numero']
            )
        ) {
            $this->newLine();

            $this->warn(
                'OFICIOS NO IMPORTADOS'
            );

            foreach (
                $resultado['descartados_con_numero']
                as $registro
            ) {
                $this->line(
                    "{$registro['numero']} — " .
                    "{$registro['motivo']}"
                );
            }
        }

        if (
            !empty(
                $resultado['descartados_sin_numero']
            )
        ) {
            $this->newLine();

            $this->warn(
                'Registros descartados sin número: ' .
                count(
                    $resultado[
                        'descartados_sin_numero'
                    ]
                )
            );
        }
    }

    private function mostrarPdf(
        array $resultado
    ): void {
        $this->newLine();

        $this->line(
            "  PDFs encontrados : " .
            $resultado['encontrados']
        );

        $this->line(
            "  PDFs asociados   : " .
            $resultado['asociados']
        );

        $this->line(
            "  Ya existentes    : " .
            $resultado['ya_existentes']
        );

        $this->line(
            "  Ignorados        : " .
            $resultado['ignorados_descartados']
        );

        if (!empty($resultado['sin_oficio'])) {
            $this->newLine();

            $this->warn(
                'PDFs SIN OFICIO CORRESPONDIENTE'
            );

            foreach (
                $resultado['sin_oficio']
                as $registro
            ) {
                $numero =
                    $registro['numero'] ??
                    'SIN NÚMERO';

                $this->line(
                    "{$numero} — {$registro['archivo']} — " .
                    "{$registro['motivo']}"
                );
            }
        }
    }

    private function mostrarFinal(
        array $importacion,
        array $pdfs
    ): void {
        $this->newLine();

        $this->info(
            '======================================'
        );

        $this->info(
            '       MIGRACIÓN COMPLETADA'
        );

        $this->info(
            '======================================'
        );

        $importados =
            collect(
                $importacion['estadisticas']
            )->sum('importados');

        $duplicados =
            collect(
                $importacion['estadisticas']
            )->sum('duplicados');

        $descartados =
            collect(
                $importacion['estadisticas']
            )->sum('descartados');

        $this->newLine();

        $this->line(
            "Oficios importados : {$importados}"
        );

        $this->line(
            "Oficios duplicados : {$duplicados}"
        );

        $this->line(
            "Oficios descartados: {$descartados}"
        );

        $this->line(
            "PDFs encontrados   : {$pdfs['encontrados']}"
        );

        $this->line(
            "PDFs asociados     : {$pdfs['asociados']}"
        );

        $this->line(
            "PDFs ya existentes : {$pdfs['ya_existentes']}"
        );

        $this->line(
            "PDFs sin oficio    : " .
            count($pdfs['sin_oficio'])
        );

        $this->line(
            "Oficios sin PDF    : " .
            count($pdfs['oficios_sin_pdf'])
        );

        if (!empty($pdfs['oficios_sin_pdf'])) {
            $this->newLine();

            $this->warn(
                'OFICIOS QUE QUEDARON SIN PDF'
            );

            foreach (
                $pdfs['oficios_sin_pdf']
                as $registro
            ) {
                $this->line(
                    $registro['numero']
                );
            }
        }

        $this->line(
            "Folios reservados  : " .
            count($importacion['reservados'])
        );
    }
}
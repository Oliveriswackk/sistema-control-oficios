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
                    ],
                    function (string $tipo, string $ruta): void {
                        $this->line(
                            "Procesando {$tipo}: {$ruta}"
                        );
                    }
                );

            $this->mostrarPdf(
                $pdfs
            );

            $this->mostrarFinal(
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
        $this->mostrarAsociados(
            $resultado['asociados_detalle']
        );

        $this->mostrarYaExistentes(
            $resultado['ya_existentes_detalle']
        );

        $this->mostrarOficiosSinPdf(
            $resultado['oficios_sin_pdf']
        );

        $this->mostrarSinOficio(
            $resultado['sin_oficio']
        );

        $this->mostrarDetalles(
            'PDFs AMBIGUOS',
            $resultado['ambiguos'],
            'warn'
        );

        $this->mostrarDetalles(
            'ERRORES AL PROCESAR PDFs',
            $resultado['errores_detalle'],
            'error'
        );

        $this->mostrarDetalles(
            'PDFs OMITIDOS POR REGLAS',
            $resultado['ignorados_detalle'],
            'warn'
        );

        $this->mostrarSinNumero(
            $resultado['sin_numero']
        );
    }

    private function mostrarAsociados(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();
        $this->info('PDFs ASOCIADOS (' . count($registros) . ')');

        foreach ($registros as $registro) {
            $this->line(
                "ID {$registro['oficio_id']} | " .
                "{$this->numeroMostrable($registro['numero'])} | " .
                "{$registro['archivo']}"
            );
        }
    }

    private function mostrarYaExistentes(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();
        $this->warn('PDFs YA EXISTENTES (' . count($registros) . ')');

        foreach ($registros as $registro) {
            $this->line(
                "ID {$registro['oficio_id']} | " .
                "{$this->numeroMostrable($registro['numero'])} | " .
                "PDF omitido: {$registro['archivo']}"
            );
        }
    }

    private function mostrarSinOficio(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();
        $this->warn('PDFs SIN OFICIO (' . count($registros) . ')');

        foreach ($registros as $registro) {
            $numero = $registro['numero'] ?? 'SIN NÚMERO';

            $this->line(
                "{$registro['archivo']} | " .
                $this->numeroMostrable($numero)
            );
        }
    }

    private function numeroMostrable(
        ?string $numero
    ): string {
        if ($numero === null || $numero === '') {
            return 'SIN NÚMERO';
        }

        return preg_replace(
            '/^[\s\-\._\/\\\\]+/u',
            '',
            $numero
        ) ?: 'SIN NÚMERO';
    }

    private function mostrarDetalles(
        string $titulo,
        array $registros,
        string $estilo
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();

        $this->{$estilo}(
            $titulo . ' (' . count($registros) . ')'
        );

        foreach ($registros as $registro) {
            $numero = $this->numeroMostrable(
                $registro['numero'] ?? null
            );
            $oficioId = $registro['oficio_id'] ?? 'SIN ID';
            $motivo = $registro['motivo'] ?? '';
            $etapa = $registro['etapa'] ?? '';

            $this->line(
                "{$registro['archivo']} | {$numero} | ID {$oficioId}" .
                ($etapa !== '' ? " | Etapa: {$etapa}" : '') .
                ($motivo !== '' ? " | {$motivo}" : '')
            );
        }
    }

    private function mostrarOficiosSinPdf(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();

        $this->warn(
            'OFICIOS SIN PDF (' . count($registros) . ')'
        );

        foreach ($registros as $registro) {
            $this->line(
                "ID {$registro['id']} | {$registro['numero']} | " .
                "Tipo {$registro['tipo_oficio_id']} " .
                "({$registro['tipo']}) | {$registro['motivo']}"
            );
        }
    }

    private function mostrarSinNumero(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();

        $this->warn(
            'PDFs SIN NÚMERO (' . count($registros) . ')'
        );

        foreach ($registros as $registro) {
            $this->line(
                "{$registro['archivo']} | {$registro['ruta']}"
            );
        }
    }

    private function mostrarFinal(
        array $pdfs
    ): void {
        $this->newLine();

        $this->info('RESUMEN');

        $this->line(
            "PDFs encontrados: {$pdfs['encontrados']}"
        );
        $this->line(
            "Asociaciones exitosas: {$pdfs['asociados']}"
        );
        $this->line(
            "Documentos ya existentes: {$pdfs['ya_existentes']}"
        );
        $this->line(
            'Oficios sin PDF: ' . count($pdfs['oficios_sin_pdf'])
        );
        $this->line(
            'PDFs sin oficio: ' . count($pdfs['sin_oficio'])
        );
        $this->line(
            'PDFs ambiguos: ' . count($pdfs['ambiguos'])
        );
        $this->line(
            "Errores técnicos: {$pdfs['errores']}"
        );
        $this->line(
            'PDFs sin número (S-N): ' . count($pdfs['sin_numero'])
        );
        $this->line(
            'Estado: ' .
            ($pdfs['errores'] > 0
                ? 'COMPLETADO CON INCIDENCIAS'
                : 'COMPLETADO')
        );
    }
}
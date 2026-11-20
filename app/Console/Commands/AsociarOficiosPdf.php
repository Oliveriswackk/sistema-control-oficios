<?php

namespace App\Console\Commands;

use App\Services\Historico\AsociarOficiosPdfService;
use Illuminate\Console\Command;

class AsociarOficiosPdf extends Command
{
    protected $signature =
        'historico:asociar-pdfs {ruta}';

    protected $description =
        'Asocia los PDFs históricos con los oficios existentes';

    public function handle(
        AsociarOficiosPdfService $service
    ): int {
        try {
            $resultado = $service->ejecutar(
                $this->argument('ruta'),
                [],
                [],
                function (string $tipo, string $ruta): void {
                    $this->line(
                        "Procesando {$tipo}: {$ruta}"
                    );
                }
            );

            $this->mostrarResultado(
                $resultado
            );

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->newLine();

            $this->error(
                'La asociación de PDFs fue cancelada.'
            );

            $this->error(
                $e->getMessage()
            );

            return Command::FAILURE;
        }
    }

    private function mostrarResultado(
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

        $this->mostrarResumen(
            $resultado
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

    private function mostrarResumen(
        array $resultado
    ): void {
        $this->newLine();
        $this->info('RESUMEN');
        $this->line(
            "PDFs encontrados: {$resultado['encontrados']}"
        );
        $this->line(
            "Asociaciones exitosas: {$resultado['asociados']}"
        );
        $this->line(
            "Documentos ya existentes: {$resultado['ya_existentes']}"
        );
        $this->line(
            'Oficios sin PDF: ' . count($resultado['oficios_sin_pdf'])
        );
        $this->line(
            'PDFs sin oficio: ' . count($resultado['sin_oficio'])
        );
        $this->line(
            'PDFs ambiguos: ' . count($resultado['ambiguos'])
        );
        $this->line(
            "Errores técnicos: {$resultado['errores']}"
        );
        $this->line(
            'PDFs sin número (S-N): ' . count($resultado['sin_numero'])
        );
        $this->line(
            'Estado: ' .
            ($resultado['errores'] > 0
                ? 'COMPLETADO CON INCIDENCIAS'
                : 'COMPLETADO')
        );
    }
}
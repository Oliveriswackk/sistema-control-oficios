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
                $this->argument('ruta')
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
        $this->newLine();

        $this->info(
            '======================================'
        );

        $this->info(
            '       ASOCIACIÓN DE PDFs COMPLETADA'
        );

        $this->info(
            '======================================'
        );

        $this->newLine();

        $this->line(
            '  PDFs encontrados : ' .
            $resultado['encontrados']
        );

        $this->line(
            '  PDFs asociados   : ' .
            $resultado['asociados']
        );

        $this->line(
            '  Ya existentes    : ' .
            $resultado['ya_existentes']
        );

        $this->line(
            '  Ignorados        : ' .
            $resultado['ignorados_descartados']
        );

        $this->mostrarSinOficio(
            $resultado['sin_oficio']
        );

        $this->mostrarSinPdf(
            $resultado['oficios_sin_pdf']
        );
    }

    private function mostrarSinOficio(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();

        $this->warn(
            'PDFs SIN OFICIO CORRESPONDIENTE'
        );

        foreach ($registros as $registro) {
            $numero =
                $registro['numero'] ??
                'SIN NÚMERO';

            $this->line(
                "{$numero} — {$registro['archivo']} — " .
                "{$registro['motivo']}"
            );
        }
    }

    private function mostrarSinPdf(
        array $registros
    ): void {
        if (empty($registros)) {
            return;
        }

        $this->newLine();

        $this->warn(
            'OFICIOS SIN PDF'
        );

        foreach ($registros as $registro) {
            $this->line(
                $registro['numero']
            );
        }
    }
}
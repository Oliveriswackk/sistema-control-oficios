<?php

namespace App\Console\Commands;

use App\Services\Historico\ImportarOficiosService;
use Illuminate\Console\Command;

class ImportarOficios extends Command
{
    protected $signature = 'app:importar-oficios {ruta}';

    protected $description =
        'Importa oficios históricos desde los CSV de una carpeta';

    public function handle(
        ImportarOficiosService $service
    ): int {
        try {
            $resultado = $service->ejecutar(
                $this->argument('ruta')
            );

            $service->mostrarResultado(
                $resultado,
                $this
            );

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->newLine();

            $this->error(
                'La importación fue cancelada.'
            );

            $this->error(
                $e->getMessage()
            );

            return Command::FAILURE;
        }
    }
}
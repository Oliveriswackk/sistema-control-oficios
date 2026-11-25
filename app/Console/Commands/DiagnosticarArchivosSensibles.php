<?php

namespace App\Console\Commands;

use App\Models\Oficio;
use App\Services\OficioArchivoStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiagnosticarArchivosSensibles extends Command
{
    protected $signature =
        'oficios:diagnosticar-archivos-sensibles {--confirmar-copia-local : Confirma que la base de datos contiene una copia local no productiva}';

    protected $description =
        'Inspecciona, sin modificar, documentos y enlaces asociados a oficios sensibles';

    public function handle(
        OficioArchivoStorageService $archivoStorage
    ): int {
        if (!$this->option('confirmar-copia-local')) {
            $this->error(
                'La inspección solo debe ejecutarse contra una copia local no productiva.'
            );

            return self::FAILURE;
        }

        if (app()->environment('production')) {
            $this->error(
                'El diagnóstico se bloqueó porque APP_ENV está configurado como production.'
            );

            return self::FAILURE;
        }

        $connection = DB::connection();
        $databaseConfig = config(
            'database.connections.' . config('database.default'),
            []
        );
        $host = $databaseConfig['host'] ?? null;

        if (
            $connection->getDriverName() !== 'sqlite' &&
            (
                !in_array($host, ['localhost', '127.0.0.1', '::1'], true) ||
                !empty($databaseConfig['url'])
            )
        ) {
            $this->error(
                'El diagnóstico solo admite SQLite o una base MariaDB/MySQL local sin URL remota.'
            );

            return self::FAILURE;
        }

        $oficios = Oficio::query()
            ->with(['tipo', 'archivos.versiones'])
            ->where('es_sensible', true)
            ->get();

        $archivos = [];
        $enlaces = [];

        foreach ($oficios as $oficio) {
            foreach ([
                'link_drive' => $oficio->link_drive,
                'link_documento' => $oficio->link_documento,
            ] as $campo => $enlace) {
                if ($enlace) {
                    $enlaces[] = [
                        'oficio_id' => $oficio->id,
                        'numero_oficio' => $oficio->numero_oficio,
                        'campo' => $campo,
                        'url' => $enlace,
                    ];
                }
            }

            foreach ($oficio->archivos as $archivo) {
                foreach ($archivo->versiones as $version) {
                    $esPrivado = $archivoStorage->isPrivatePath(
                        $version->ruta
                    );

                    $disco = $esPrivado ? 'local' : 'public';
                    $existe = Storage::disk($disco)->exists(
                        $version->ruta
                    );

                    $archivos[] = [
                        'oficio_id' => $oficio->id,
                        'numero_oficio' => $oficio->numero_oficio,
                        'tipo_oficio' => $oficio->tipo?->clave,
                        'archivo_id' => $archivo->id,
                        'version_id' => $version->id,
                        'version' => $version->version,
                        'ruta' => $version->ruta,
                        'disco_actual' => $disco,
                        'es_publica' => $version->es_publica,
                        'existe' => $existe,
                        'hash_sha256' => $version->hash_sha256,
                        'requiere_migracion' => !$esPrivado && $existe,
                    ];
                }
            }
        }

        $compartidos = collect($archivos)
            ->filter(fn ($archivo) => $archivo['hash_sha256'] !== null)
            ->groupBy('hash_sha256')
            ->filter(function ($grupo) {
                return $grupo
                    ->pluck('oficio_id')
                    ->unique()
                    ->count() > 1;
            })
            ->map(fn ($grupo) => $grupo->values())
            ->values();

        $rutasCompartidas = collect($archivos)
            ->groupBy('ruta')
            ->filter(function ($grupo) {
                return $grupo
                    ->pluck('oficio_id')
                    ->unique()
                    ->count() > 1;
            })
            ->map(fn ($grupo) => $grupo->values())
            ->values();

        $this->line(json_encode([
            'oficios_sensibles' => $oficios->count(),
            'archivos' => $archivos,
            'archivos_compartidos_por_hash' => $compartidos,
            'rutas_compartidas' => $rutasCompartidas,
            'enlaces_externos' => $enlaces,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}

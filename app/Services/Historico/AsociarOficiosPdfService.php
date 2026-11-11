<?php

namespace App\Services\Historico;

use App\Models\Oficio;
use App\Models\OficioArchivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AsociarOficiosPdfService
{
    private const USUARIO_MIGRACION = 1;

    public function ejecutar(
        string $rutaHistorico,
        array $numerosDescartados = [],
        array $oficiosObjetivo = []
    ): array {
        $rutaHistorico = rtrim(
            $rutaHistorico,
            DIRECTORY_SEPARATOR
        );

        $rutaPdf = $rutaHistorico .
            DIRECTORY_SEPARATOR .
            'pdfs';

        if (!is_dir($rutaPdf)) {
            throw new \RuntimeException(
                "No existe la carpeta de PDFs: {$rutaPdf}"
            );
        }

        /*
         * Se permiten los nombres actuales y los anteriores
         * para conservar compatibilidad con otras migraciones.
         */
        $rutas = [
            'Enviados' => [
                $rutaPdf . DIRECTORY_SEPARATOR . 'enviado',
                $rutaPdf . DIRECTORY_SEPARATOR . 'enviados',
            ],

            'Recibidos' => [
                $rutaPdf . DIRECTORY_SEPARATOR . 'recibido',
                $rutaPdf . DIRECTORY_SEPARATOR . 'recibidos',
                $rutaPdf . DIRECTORY_SEPARATOR . 'recibidoCPC',
            ],
        ];

        $descartados = [];

        foreach ($numerosDescartados as $numero) {
            $descartados[
                $this->normalizarNumero($numero)
            ] = true;
        }

        $resultado = [
            'encontrados' => 0,
            'asociados' => 0,
            'ya_existentes' => 0,
            'ignorados_descartados' => 0,
            'ignorados_acta' => 0,
            'sin_oficio' => [],
            'oficios_sin_pdf' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($rutas as $tipo => $directorios) {
                foreach ($directorios as $ruta) {
                    if (!is_dir($ruta)) {
                        continue;
                    }

                    $this->procesarDirectorio(
                        $ruta,
                        $tipo,
                        $descartados,
                        $resultado
                    );
                }
            }

            /*
             * La consulta se hace contra la base de datos,
             * no solamente contra los PDFs encontrados en esta ejecución.
             *
             * Así no reportamos como faltante un oficio que ya tenía
             * un documento asociado antes de ejecutar la migración.
             */
            $resultado['oficios_sin_pdf'] =
                $this->obtenerOficiosSinPdf(
                    $oficiosObjetivo
                );

            DB::commit();

            return $resultado;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }

    private function procesarDirectorio(
        string $ruta,
        string $tipo,
        array $descartados,
        array &$resultado
    ): void {
        $archivos = $this->obtenerPdfs($ruta);

        foreach ($archivos as $archivo) {
            $resultado['encontrados']++;

            /*
             * Ignorar ACTA sin importar mayúsculas/minúsculas.
             */
            if (
                stripos(
                    pathinfo($archivo, PATHINFO_FILENAME),
                    'ACTA'
                ) !== false
            ) {
                $resultado['ignorados_acta']++;
                continue;
            }

            $numero = $this->obtenerNumeroDesdePdf(
                $archivo,
                $tipo
            );

            if ($numero === null) {
                $resultado['sin_oficio'][] = [
                    'archivo' => basename($archivo),
                    'numero' => null,
                    'motivo' =>
                        'No fue posible obtener número de oficio',
                ];

                continue;
            }

            $numeroNormalizado = $this->normalizarNumero(
                $numero
            );

            if (isset($descartados[$numeroNormalizado])) {
                $resultado['ignorados_descartados']++;
                continue;
            }

            $oficio = Oficio::where(
                'numero_oficio',
                $numero
            )->first();

            if (!$oficio) {
                $oficio = $this->buscarOficioNormalizado(
                    $numero
                );
            }

            if (!$oficio) {
                $resultado['sin_oficio'][] = [
                    'archivo' => basename($archivo),
                    'numero' => $numero,
                    'motivo' =>
                        'No existe un oficio correspondiente',
                ];

                continue;
            }

            /*
             * PROTECCIÓN CONTRA SOBRESCRITURA
             *
             * Si el oficio ya tiene cualquier versión de archivo,
             * no se crea una versión nueva, aunque el hash sea distinto.
             *
             * Esto evita reemplazar documentos que ya estaban cargados.
             */
            $yaTieneDocumento = OficioArchivo::where(
                'oficio_id',
                $oficio->id
            )
            ->whereHas('versiones')
            ->exists();

            if ($yaTieneDocumento) {
                $resultado['ya_existentes']++;
                continue;
            }

            $hash = hash_file('sha256', $archivo);

            if ($hash === false) {
                $resultado['sin_oficio'][] = [
                    'archivo' => basename($archivo),
                    'numero' => $numero,
                    'motivo' =>
                        'No fue posible calcular el hash del archivo',
                ];

                continue;
            }

            $this->asociarPdf(
                $archivo,
                $oficio,
                $hash
            );

            $resultado['asociados']++;
        }
    }

    private function obtenerPdfs(string $ruta): array
    {
        $resultado = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $ruta,
                \FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $archivo) {
            if (
                $archivo->isFile() &&
                mb_strtolower($archivo->getExtension()) === 'pdf'
            ) {
                $resultado[] = $archivo->getPathname();
            }
        }

        // Orden estable para que el procesamiento sea reproducible.
        sort($resultado, SORT_NATURAL | SORT_FLAG_CASE);

        return $resultado;
    }

    private function obtenerNumeroDesdePdf(
        string $ruta,
        string $tipo
    ): ?string {
        $nombre = pathinfo(
            $ruta,
            PATHINFO_FILENAME
        );

        $nombre = trim($nombre);

        if ($nombre === '') {
            return null;
        }

        /*
         * Los recibidos pueden llevar fecha al inicio:
         * 2026-01-15-NUMERO-OFICIO.pdf
         *
         * Se elimina únicamente ese prefijo.
         */
        if ($tipo === 'Recibidos') {
            $nombre = preg_replace(
                '/^\d{4}-\d{2}-\d{2}-/',
                '',
                $nombre
            );
        }

        $nombre = trim($nombre);

        return $nombre !== ''
            ? $nombre
            : null;
    }

    private function buscarOficioNormalizado(
        string $numero
    ): ?Oficio {
        $normalizado = $this->normalizarNumero($numero);

        $oficios = Oficio::query()
            ->select([
                'id',
                'numero_oficio',
            ])
            ->get();

        foreach ($oficios as $oficio) {
            if (
                $this->normalizarNumero(
                    $oficio->numero_oficio
                ) === $normalizado
            ) {
                return $oficio;
            }
        }

        return null;
    }

    private function normalizarNumero(
        string $numero
    ): string {
        $numero = trim($numero);

        $numero = str_replace(
            [
                '–',
                '—',
                '−',
            ],
            '-',
            $numero
        );

        $numero = preg_replace(
            '/\s+/u',
            ' ',
            $numero
        );

        return mb_strtoupper(
            trim($numero)
        );
    }

    private function asociarPdf(
        string $rutaOrigen,
        Oficio $oficio,
        string $hash
    ): void {
        $nombreOriginal = basename($rutaOrigen);

        $mimeType = 'application/pdf';

        /*
         * Si existe un registro de archivo sin versiones,
         * se reutiliza en lugar de crear otro.
         */
        $archivo = OficioArchivo::firstOrCreate(
            [
                'oficio_id' => $oficio->id,
            ],
            [
                'nombre_original' => $nombreOriginal,
                'tipo_archivo' => $mimeType,
                'nivel_acceso' => 'publico',
            ]
        );

        $archivo->update([
            'nombre_original' => $nombreOriginal,
            'tipo_archivo' => $mimeType,
        ]);

        $ultimaVersion = $archivo->versiones()->max('version');

        $version = ($ultimaVersion ?? 0) + 1;

        $path = "oficios/{$oficio->id}/{$nombreOriginal}";

        $contenido = file_get_contents($rutaOrigen);

        if ($contenido === false) {
            throw new \RuntimeException(
                "No fue posible leer el PDF: {$rutaOrigen}"
            );
        }

        $guardado = Storage::disk('public')->put(
            $path,
            $contenido
        );

        if (!$guardado) {
            throw new \RuntimeException(
                "No fue posible guardar el PDF: {$nombreOriginal}"
            );
        }

        $versionAnteriorId = $archivo->versiones()
            ->where('version', $version - 1)
            ->value('id');

        $archivo->versiones()->update([
            'es_actual' => false,
        ]);

        $archivo->versiones()->create([
            'ruta' => $path,
            'mime_type' => $mimeType,
            'tamano' => filesize($rutaOrigen),
            'hash_sha256' => $hash,
            'version' => $version,
            'es_actual' => true,
            'es_publica' => true,
            'subido_por_id' => self::USUARIO_MIGRACION,
            'motivo_reemplazo' => null,
            'version_anterior_id' => $versionAnteriorId,
        ]);

        $oficio->historial()->create([
            'usuario_id' => self::USUARIO_MIGRACION,
            'accion' => 'archivo_subido',
            'descripcion' =>
                'Se cargó el documento histórico del oficio',
            'entidad_relacionada' => 'oficio_archivo',
            'entidad_relacionada_id' => $archivo->id,
        ]);
    }

    private function obtenerOficiosSinPdf(
        array $oficiosObjetivo
    ): array {
        if (empty($oficiosObjetivo)) {
            return [];
        }

        $ids = array_values(
            array_unique($oficiosObjetivo)
        );


        $oficiosConVersion = OficioArchivo::whereIn(
            'oficio_id',
            $ids
        )
        ->whereHas('versiones')
        ->pluck('oficio_id')
        ->unique()
        ->flip();

        $oficios = Oficio::whereIn('id', $ids)->get();

        $resultado = [];

        foreach ($oficios as $oficio) {
            if (!isset($oficiosConVersion[$oficio->id])) {
                $resultado[] = [
                    'id' => $oficio->id,
                    'numero' => $oficio->numero_oficio,
                ];
            }
        }

        return $resultado;
    }
}
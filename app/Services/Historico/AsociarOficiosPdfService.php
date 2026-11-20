<?php

namespace App\Services\Historico;

use App\Models\Oficio;
use App\Models\OficioArchivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AsociarOficiosPdfService
{
    private const USUARIO_MIGRACION = 1;

    private const TIPO_ENVIADO = 1;

    private const TIPO_RECIBIDO = 2;

    public function ejecutar(
        string $rutaHistorico,
        array $numerosDescartados = [],
        array $oficiosObjetivo = [],
        ?callable $progreso = null
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

        $rutas = [
            'Enviados' => [
                'directorios' => [
                    $rutaPdf . DIRECTORY_SEPARATOR . 'enviado',
                    $rutaPdf . DIRECTORY_SEPARATOR . 'enviados',
                ],
                'tipos' => [self::TIPO_ENVIADO],
            ],

            'Recibidos' => [
                'directorios' => [
                    $rutaPdf . DIRECTORY_SEPARATOR . 'recibido',
                    $rutaPdf . DIRECTORY_SEPARATOR . 'recibidos',
                    $rutaPdf . DIRECTORY_SEPARATOR . 'recibidoCPC',
                ],
                /*
                 * El importador histórico guarda Recibidos y Recibidos
                 * CPC como tipo recibido (2). El catálogo separado
                 * recibido_cpc (3) corresponde a la captura actual.
                 */
                'tipos' => [self::TIPO_RECIBIDO],
            ],
        ];

        // Preparar números descartados con la misma normalización.
        $descartados = [];

        foreach ($numerosDescartados as $numero) {
            $clave = $this->normalizarNumero($numero);

            if ($clave !== '') {
                $descartados[$clave] = true;
            }
        }

        $resultado = [
            'encontrados' => 0,
            'asociados' => 0,
            'ya_existentes' => 0,
            'ya_existentes_detalle' => [],
            'ignorados_descartados' => 0,
            'ignorados_acta' => 0,
            'ignorados' => 0,
            'ignorados_detalle' => [],
            'sin_oficio' => [],
            'ambiguos' => [],
            'errores' => 0,
            'errores_detalle' => [],
            'asociados_detalle' => [],
            'sin_numero' => [],
            'oficios_sin_pdf' => [],
        ];

        /*
         * Cargar los oficios una sola vez y construir un índice.
         * Las claves se agrupan por tipo para evitar asociar un PDF
         * con un oficio de otra categoría.
         */
        $indiceOficios = $this->crearIndiceOficios();

        DB::beginTransaction();

        try {
            foreach ($rutas as $tipo => $configuracion) {
                foreach ($configuracion['directorios'] as $ruta) {
                    if (!is_dir($ruta)) {
                        continue;
                    }

                    if ($progreso !== null) {
                        $progreso($tipo, $ruta);
                    }

                    $this->procesarDirectorio(
                        $ruta,
                        $tipo,
                        $descartados,
                        $indiceOficios,
                        $configuracion['tipos'],
                        $resultado
                    );
                }
            }

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

    /**
     * Construye un índice de oficios por número normalizado.
     *
     * Cada clave puede contener uno o varios registros.
     * Si contiene varios, la coincidencia se considera ambigua.
     */
    private function crearIndiceOficios(): array
    {
        $indice = [];

        $oficios = Oficio::query()
            ->get();

        foreach ($oficios as $oficio) {
            if (!$oficio->numero_oficio) {
                continue;
            }

            $clave = $this->normalizarNumero(
                $oficio->numero_oficio
            );

            if ($clave === '') {
                continue;
            }

            $tipoOficio = (int) $oficio->tipo_oficio_id;

            if (!isset($indice[$tipoOficio][$clave])) {
                $indice[$tipoOficio][$clave] = [];
            }

            $indice[$tipoOficio][$clave][] = $oficio;
        }

        return $indice;
    }

    private function procesarDirectorio(
        string $ruta,
        string $tipo,
        array $descartados,
        array $indiceOficios,
        array $tiposOficio,
        array &$resultado
    ): void {
        $archivos = $this->obtenerPdfs($ruta);

        foreach ($archivos as $archivo) {
            $resultado['encontrados']++;

            $nombreArchivo = pathinfo(
                $archivo,
                PATHINFO_FILENAME
            );

            // Ignorar ACTAS.
            if (stripos($nombreArchivo, 'ACTA') !== false) {
                $resultado['ignorados_acta']++;
                $resultado['ignorados']++;
                $resultado['ignorados_detalle'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                    'numero' => null,
                    'oficio_id' => null,
                    'motivo' => 'Archivo ACTA excluido',
                ];
                continue;
            }

            $numero = $this->obtenerNumeroDesdePdf(
                $archivo,
                $tipo
            );

            if ($numero === null) {
                $resultado['sin_oficio'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                    'numero' => null,
                    'oficio_id' => null,
                    'motivo' =>
                        'No fue posible obtener número de oficio',
                ];

                continue;
            }

            if ($this->esNumeroSinNumero($numero)) {
                $resultado['sin_numero'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                ];

                continue;
            }

            if ($this->contieneNumeroDelimitado(
                $numero,
                array_keys($descartados)
            )) {
                $resultado['ignorados_descartados']++;
                $resultado['ignorados']++;
                $resultado['ignorados_detalle'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                    'numero' => $numero,
                    'oficio_id' => null,
                    'motivo' => 'Número incluido en descartados',
                ];
                continue;
            }

            // Buscar coincidencia mediante el índice.
            try {
                $oficio = $this->buscarOficioNormalizado(
                    $numero,
                    $indiceOficios,
                    $tiposOficio
                );

            } catch (\RuntimeException $e) {
                $resultado['ambiguos'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                    'numero' => $numero,
                    'oficio_id' => null,
                    'motivo' => $e->getMessage(),
                ];

                continue;
            }

            if (!$oficio) {
                $resultado['sin_oficio'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                    'numero' => $numero,
                    'oficio_id' => null,
                    'motivo' =>
                        'No existe un oficio correspondiente',
                ];

                continue;
            }

            // No sobrescribir documentos existentes.
            try {
                $yaTieneDocumento = OficioArchivo::where(
                    'oficio_id',
                    $oficio->id
                )
                ->whereHas('versiones')
                ->exists();

            } catch (\Throwable $e) {
                $this->registrarError(
                    $resultado,
                    $archivo,
                    $oficio->numero_oficio,
                    $oficio->id,
                    'validar documento existente',
                    $e
                );

                continue;
            }

            if ($yaTieneDocumento) {
                $resultado['ya_existentes']++;
                $resultado['ya_existentes_detalle'][] = [
                    'archivo' => basename($archivo),
                    'ruta' => $archivo,
                    'numero' => $oficio->numero_oficio,
                    'oficio_id' => $oficio->id,
                    'motivo' =>
                        'El oficio ya tiene un documento asociado; ' .
                        'no se sobrescribió',
                ];
                continue;
            }

            $hash = hash_file('sha256', $archivo);

            if ($hash === false) {
                $this->registrarError(
                    $resultado,
                    $archivo,
                    $oficio->numero_oficio,
                    $oficio->id,
                    'calcular hash',
                    null,
                    'No fue posible calcular el hash del archivo'
                );

                continue;
            }

            try {
                DB::transaction(
                    function () use (
                        $archivo,
                        $oficio,
                        $hash
                    ): void {
                        $this->asociarPdf(
                            $archivo,
                            $oficio,
                            $hash
                        );
                    }
                );

            } catch (\Throwable $e) {
                $this->registrarError(
                    $resultado,
                    $archivo,
                    $oficio->numero_oficio,
                    $oficio->id,
                    'almacenar PDF',
                    $e
                );

                continue;
            }

            $resultado['asociados']++;
            $resultado['asociados_detalle'][] = [
                'archivo' => basename($archivo),
                'ruta' => $archivo,
                'numero' => $oficio->numero_oficio,
                'oficio_id' => $oficio->id,
                'motivo' => 'PDF asociado correctamente',
            ];
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

        sort($resultado, SORT_NATURAL | SORT_FLAG_CASE);

        return $resultado;
    }

    /**
     * Obtiene el número de oficio desde el nombre del PDF.
     */
    private function obtenerNumeroDesdePdf(
        string $ruta,
        string $tipo
    ): ?string {
        $nombre = trim(
            pathinfo($ruta, PATHINFO_FILENAME)
        );

        if ($nombre === '') {
            return null;
        }

        if ($tipo === 'Enviados') {
            /*
             * Formato esperado:
             * SESEA-COORDINACION-CONSECUTIVO-AÑO
             *
             * Lo que aparezca después del año se considera
             * descripción del documento y no forma parte
             * del número de oficio.
             *
             * Se permiten distintos separadores.
             */
            if (preg_match(
                '/^(SESEA)[^\p{L}\d]+([A-Z0-9]+)[^\p{L}\d]+(\d+)[^\p{L}\d]+(\d{4})(?=$|[^\p{L}\d])/iu',
                $nombre,
                $coincidencias
            )) {
                return strtoupper(
                    $coincidencias[1] . '-' .
                    $coincidencias[2] . '-' .
                    $coincidencias[3] . '-' .
                    $coincidencias[4]
                );
            }

            return null;
        }

        if ($tipo === 'Recibidos') {
            /*
             * La fecha es opcional. Si los primeros ocho caracteres son
             * dígitos, se elimina únicamente ese prefijo; el resto se
             * conserva completo como identificador.
             */
            if (preg_match('/^\d{8}/u', $nombre) === 1) {
                $nombre = substr($nombre, 8);
            }

            $numero = trim($nombre);

            return $numero !== ''
                ? mb_strtoupper($numero, 'UTF-8')
                : null;
        }

        return null;
    }

    /**
     * Busca un oficio mediante su clave normalizada.
     */
    private function buscarOficioNormalizado(
        string $nombre,
        array $indiceOficios,
        array $tiposOficio
    ): ?Oficio {
        $tokens = $this->obtenerTokensNormalizados($nombre);

        if (empty($tokens)) {
            return null;
        }

        $coincidencias = [];

        foreach ($tiposOficio as $tipoOficio) {
            foreach ($indiceOficios[$tipoOficio] ?? [] as $clave => $oficios) {
                if ($this->claveApareceEnTokens($clave, $tokens)) {
                    $coincidencias = array_merge($coincidencias, $oficios);
                }
            }
        }

        if (count($coincidencias) > 1) {
            $detalles = array_map(
                function ($oficio) {
                    return "ID {$oficio->id} ({$oficio->numero_oficio})";
                },
                $coincidencias
            );

            throw new \RuntimeException(
                "Número de oficio ambiguo en el nombre: {$nombre}. " .
                "Candidatos: " .
                implode(', ', $detalles)
            );
        }

        return $coincidencias[0] ?? null;
    }

    private function esNumeroSinNumero(string $nombre): bool
    {
        return $this->contieneNumeroDelimitado(
            $nombre,
            [
                'SN',
                'SINNUMERO',
                'SINNÚMERO',
            ]
        );
    }

    private function registrarError(
        array &$resultado,
        string $archivo,
        ?string $numero,
        ?int $oficioId,
        string $etapa,
        ?\Throwable $exception = null,
        ?string $motivo = null
    ): void {
        $resultado['errores']++;
        $resultado['errores_detalle'][] = [
            'archivo' => basename($archivo),
            'ruta' => $archivo,
            'numero' => $numero,
            'oficio_id' => $oficioId,
            'etapa' => $etapa,
            'motivo' => $motivo ??
                ($exception
                    ? $exception->getMessage()
                    : 'Error no especificado'),
        ];
    }

    /**
     * Determina si alguno de los identificadores aparece como una
     * secuencia completa de tokens alfanuméricos.
     */
    private function contieneNumeroDelimitado(
        string $nombre,
        array $claves
    ): bool {
        $tokens = $this->obtenerTokensNormalizados($nombre);

        foreach ($claves as $clave) {
            if ($this->claveApareceEnTokens($clave, $tokens)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convierte un nombre en tokens delimitados por caracteres no
     * alfanuméricos. Los tokens conservan todos sus dígitos.
     */
    private function obtenerTokensNormalizados(string $nombre): array
    {
        $tokens = preg_split(
            '/[^\p{L}\d]+/u',
            $nombre,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if (!$tokens) {
            return [];
        }

        return array_map(
            function ($token) {
                return mb_strtoupper($token, 'UTF-8');
            },
            $tokens
        );
    }

    /**
     * Busca una clave completa en uno o más tokens contiguos.
     */
    private function claveApareceEnTokens(
        string $clave,
        array $tokens
    ): bool {
        if ($clave === '' || empty($tokens)) {
            return false;
        }

        $cantidadTokens = count($tokens);

        for ($inicio = 0; $inicio < $cantidadTokens; $inicio++) {
            $concatenado = '';

            for (
                $fin = $inicio;
                $fin < $cantidadTokens;
                $fin++
            ) {
                $concatenado .= $tokens[$fin];

                if ($concatenado === $clave) {
                    return true;
                }

                if (strlen($concatenado) >= strlen($clave)) {
                    break;
                }
            }
        }

        return false;
    }

    /**
     * Normaliza un número de oficio.
     *
     * Reglas:
     * - Convierte las letras a mayúsculas.
     * - Elimina cualquier separador u otro signo de puntuación.
     * - Conserva exactamente todas las letras y dígitos en su orden.
     *
     * Ejemplos:
     * SESEA-CJ-002-2026  => SESEACJ0022026
     * SESEA/CJ/002/2026  => SESEACJ0022026
     * OIC-0418            => OIC0418
     */
    private function normalizarNumero(
        string $numero
    ): string {
        $numero = mb_strtoupper(trim($numero), 'UTF-8');

        if ($numero === '') {
            return '';
        }

        $normalizado = preg_replace(
            '/[^\p{L}\d]+/u',
            '',
            $numero
        );

        return $normalizado ?? '';
    }

    private function asociarPdf(
        string $rutaOrigen,
        Oficio $oficio,
        string $hash
    ): void {
        $nombreOriginal = basename($rutaOrigen);

        $mimeType = 'application/pdf';

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

        $oficios = Oficio::with('tipo')
            ->whereIn('id', $ids)
            ->get();

        $resultado = [];

        foreach ($oficios as $oficio) {
            if (!isset($oficiosConVersion[$oficio->id])) {
                $resultado[] = [
                    'id' => $oficio->id,
                    'numero' => $oficio->numero_oficio,
                    'tipo_oficio_id' => $oficio->tipo_oficio_id,
                    'tipo' => $oficio->tipo
                        ? $oficio->tipo->nombre
                        : 'Desconocido',
                    'motivo' =>
                        'No se encontró un PDF correspondiente',
                ];
            }
        }

        return $resultado;
    }
}

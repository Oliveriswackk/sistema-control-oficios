<?php

namespace App\Services\Historico;

use App\Models\ConsecutivoOficio;
use App\Models\Coordinacion;
use App\Models\FolioReservado;
use App\Models\Oficio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportarOficiosService
{
    private const USUARIO_MIGRACION = 1;

    private const TIPO_ENVIADO = 1;
    private const TIPO_RECIBIDO = 2;

    private const ESTADO_CERRADO = 5;

    public function ejecutar(string $rutaOficios): array
    {
        $rutaOficios = rtrim(
            $rutaOficios,
            DIRECTORY_SEPARATOR
        );

        if (!is_dir($rutaOficios)) {
            throw new \RuntimeException(
                "No existe la carpeta de oficios: {$rutaOficios}"
            );
        }

        $archivos = $this->detectarArchivos(
            $rutaOficios
        );

        if (
            $archivos['enviados'] === null &&
            $archivos['recibidos'] === null
        ) {
            throw new \RuntimeException(
                'No se encontraron los CSV de Enviados ni Recibidos.'
            );
        }

        $anioHistorico = basename(
            dirname($rutaOficios)
        );

        if (!preg_match('/^\d{4}$/', $anioHistorico)) {
            throw new \RuntimeException(
                'No fue posible determinar el año histórico desde la carpeta.'
            );
        }

        $resultado = [
            'estadisticas' => [
                'Enviados' => [
                    'leidos' => 0,
                    'importados' => 0,
                    'duplicados' => 0,
                    'descartados' => 0,
                    'reservados' => 0,
                    'reservados_existentes' => 0,
                ],
                'Recibidos' => [
                    'leidos' => 0,
                    'importados' => 0,
                    'duplicados' => 0,
                    'descartados' => 0,
                    'reservados' => 0,
                    'reservados_existentes' => 0,
                ],
            ],

            'reservados' => [],

            'descartados_sin_numero' => [],
            'descartados_con_numero' => [],

            'oficios_objetivo' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($archivos as $tipo => $ruta) {
                if ($ruta === null) {
                    continue;
                }

                $tipoOficio = $tipo === 'enviados'
                    ? self::TIPO_ENVIADO
                    : self::TIPO_RECIBIDO;

                $nombre = $tipo === 'enviados'
                    ? 'Enviados'
                    : 'Recibidos';

                $this->importarArchivo(
                    $ruta,
                    $tipoOficio,
                    $nombre,
                    (int) $anioHistorico,
                    $resultado
                );
            }

            DB::commit();

            return $resultado;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }

    private function detectarArchivos(
        string $rutaOficios
    ): array {
        $archivos = glob(
            $rutaOficios .
            DIRECTORY_SEPARATOR .
            '*.csv'
        );

        $resultado = [
            'enviados' => null,
            'recibidos' => null,
        ];

        foreach ($archivos as $archivo) {
            $nombre = mb_strtoupper(
                basename($archivo)
            );

            if (
                str_contains($nombre, 'ENVIADOS') &&
                !str_contains($nombre, 'CPC')
            ) {
                $resultado['enviados'] = $archivo;
            }

            if (
                str_contains($nombre, 'RECIBIDOS') &&
                !str_contains($nombre, 'CPC')
            ) {
                $resultado['recibidos'] = $archivo;
            }
        }

        return $resultado;
    }

    private function importarArchivo(
        string $ruta,
        int $tipoOficio,
        string $nombre,
        int $anioHistorico,
        array &$resultado
    ): void {
        $handle = fopen($ruta, 'r');

        if ($handle === false) {
            throw new \RuntimeException(
                "No fue posible abrir el archivo {$ruta}."
            );
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            throw new \RuntimeException(
                "El archivo {$nombre} está vacío."
            );
        }

        $headers = $this->normalizarHeaders(
            $headers,
            $tipoOficio
        );

        $numeroFila = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $numeroFila++;

            $resultado['estadisticas'][$nombre]['leidos']++;

            if ($this->filaVacia($row)) {
                $this->descartar(
                    $resultado,
                    $nombre,
                    $numeroFila,
                    null,
                    'Fila vacía'
                );

                continue;
            }

            $data = $this->crearRegistroDesdeFila(
                $headers,
                $row
            );

            $numeroOficio = trim(
                $data['numero_oficio'] ?? ''
            );

            if ($numeroOficio === '') {
                $this->descartar(
                    $resultado,
                    $nombre,
                    $numeroFila,
                    null,
                    'Sin número de oficio'
                );

                continue;
            }

            if ($this->esSinNumero($numeroOficio)) {
                $this->descartar(
                    $resultado,
                    $nombre,
                    $numeroFila,
                    null,
                    'Sin número de oficio'
                );

                continue;
            }

            if ($this->esInicializacion($numeroOficio)) {
                $this->descartar(
                    $resultado,
                    $nombre,
                    $numeroFila,
                    $numeroOficio,
                    'Registro 000 / inicialización'
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | RESERVADOS HISTÓRICOS
            |--------------------------------------------------------------------------
            |
            | Los folios terminados en 1899 representan números reservados.
            | Se guardan como FolioReservado para el año de la migración.
            |
            | Para desactivar esta funcionalidad posteriormente, basta con
            | comentar este bloque completo.
            |
            */

            if (
                $tipoOficio === self::TIPO_ENVIADO &&
                $this->esFolioReservadoHistorico(
                    $numeroOficio
                )
            ) {
                if (
                    $this->crearFolioReservadoHistorico(
                        $numeroOficio,
                        $anioHistorico,
                        $numeroFila,
                        $resultado
                    )
                ) {
                    continue;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FIN RESERVADOS HISTÓRICOS
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | VALIDACIÓN DE AÑO PARA ENVIADOS
            |--------------------------------------------------------------------------
            |
            | Solo los oficios enviados de SESEA tienen un año codificado
            | en el número que debe coincidir con el año de la migración.
            |
            | Los recibidos NO pasan por esta validación.
            |
            */

            if ($tipoOficio === self::TIPO_ENVIADO) {
                $anioNumero = $this->obtenerAnioDelNumero(
                    $numeroOficio
                );

                if (
                    $anioNumero !== null &&
                    $anioNumero !== $anioHistorico
                ) {
                    $this->descartar(
                        $resultado,
                        $nombre,
                        $numeroFila,
                        $numeroOficio,
                        "Oficio de otro año ({$anioNumero})"
                    );

                    continue;
                }
            }

            $oficioExistente = Oficio::where(
                'numero_oficio',
                $numeroOficio
            )->first();

            if ($oficioExistente) {
                $resultado['estadisticas'][$nombre]['duplicados']++;

                $resultado['oficios_objetivo'][$numeroOficio] =
                    $oficioExistente->id;

                continue;
            }

            $fechaOficio = $this->normalizarFecha(
                $data['fecha_oficio'] ?? null
            );

            if (!$fechaOficio) {
                $this->descartar(
                    $resultado,
                    $nombre,
                    $numeroFila,
                    $numeroOficio,
                    'Sin fecha de oficio válida'
                );

                continue;
            }

            $fechaRecepcion = $this->normalizarFecha(
                $data['fecha_recepcion'] ?? null
            );

            if (!$fechaRecepcion) {
                $fechaRecepcion = $fechaOficio;
            }

            $coordinacionId = null;

            if ($tipoOficio === self::TIPO_ENVIADO) {
                $nombreCoordinacion = trim(
                    $data['coordinacion'] ?? ''
                );

                if ($nombreCoordinacion !== '') {
                    $coordinacionId = Coordinacion::where(
                        'nombre',
                        $nombreCoordinacion
                    )->value('id');
                }
            }

            $oficio = Oficio::create([
                'uuid' => (string) Str::uuid(),

                'numero_oficio' => $numeroOficio,

                'consecutivo' => $this->obtenerConsecutivo(
                    $numeroOficio,
                    $tipoOficio
                ),

                'tipo_oficio_id' => $tipoOficio,
                'estado_id' => self::ESTADO_CERRADO,

                'asunto' => trim(
                    $data['asunto'] ?? ''
                ) ?: 'Sin asunto',

                'descripcion' => null,

                'fecha_oficio' => $fechaOficio,
                'fecha_recepcion' => $fechaRecepcion,
                'fecha_limite' => null,

                'requiere_respuesta' => false,
                'es_sensible' => false,

                'respuesta_a_oficio_id' => null,

                'remitente_nombre' =>
                    $this->limpiar(
                        $data['remitente_nombre'] ?? null
                    ),

                'remitente_cargo' =>
                    $this->limpiar(
                        $data['remitente_cargo'] ?? null
                    ),

                'remitente_dependencia' =>
                    $this->limpiar(
                        $data['remitente_dependencia'] ?? null
                    ),

                'destinatario_nombre' =>
                    $this->limpiar(
                        $data['destinatario_nombre'] ?? null
                    ),

                'destinatario_cargo' =>
                    $this->limpiar(
                        $data['destinatario_cargo'] ?? null
                    ),

                'destinatario_dependencia' =>
                    $this->limpiar(
                        $data['destinatario_dependencia'] ?? null
                    ),

                'quien_elabora_nombre' =>
                    $this->limpiar(
                        $data['quien_elabora_nombre'] ?? null
                    ),

                'quien_elabora_cargo' =>
                    $this->limpiar(
                        $data['quien_elabora_cargo'] ?? null
                    ),

                'responsable_inicial_id' => null,
                'coordinacion_origen_id' => $coordinacionId,

                'usuario_registro_id' =>
                    self::USUARIO_MIGRACION,

                'link_documento' =>
                    $this->extraerUrl(
                        $data['link'] ?? null
                    ),

                'respondido_en' => null,
                'cerrado_en' => null,
                'cancelado_en' => null,
            ]);

            $resultado['estadisticas'][$nombre]['importados']++;

            $resultado['oficios_objetivo'][$numeroOficio] =
                $oficio->id;
        }

        fclose($handle);
    }

    private function esFolioReservadoHistorico(
        string $numeroOficio
    ): bool {
        return (bool) preg_match(
            '/^SESEA-[^-]+-\d+-1899$/i',
            trim($numeroOficio)
        );
    }

    private function crearFolioReservadoHistorico(
        string $numeroOficio,
        int $anioHistorico,
        int $numeroFila,
        array &$resultado
    ): bool {
        if (
            !preg_match(
                '/^SESEA-([^-]+)-(\d+)-1899$/i',
                trim($numeroOficio),
                $matches
            )
        ) {
            return false;
        }

        $clave = mb_strtoupper(
            trim($matches[1])
        );

        $numero = (int) $matches[2];

        if ($numero <= 0) {
            return false;
        }

        $coordinacionId = Coordinacion::where(
            'clave',
            $clave
        )->value('id');

        if (!$coordinacionId) {
            $this->descartar(
                $resultado,
                'Enviados',
                $numeroFila,
                $numeroOficio,
                "No existe la coordinación con clave {$clave}"
            );

            return true;
        }

        $folio = FolioReservado::where(
            'coordinacion_id',
            $coordinacionId
        )
        ->where('anio', $anioHistorico)
        ->where('numero', $numero)
        ->first();

        if (!$folio) {
            $folio = FolioReservado::create([
                'coordinacion_id' => $coordinacionId,
                'anio' => $anioHistorico,
                'numero' => $numero,
                'numero_oficio' => null,
                'estado' => 'reservado',
                'grupo_uuid' => null,
                'usuario_reserva_id' =>
                    self::USUARIO_MIGRACION,
                'motivo_cancelacion' => null,
            ]);

            $resultado['estadisticas']['Enviados']['reservados']++;
        } else {
            $resultado['estadisticas']['Enviados']['reservados_existentes']++;
        }

        $this->actualizarConsecutivoPorReservado(
            $coordinacionId,
            $anioHistorico,
            $numero
        );

        $resultado['reservados'][] = [
            'id' => $folio->id,
            'numero_oficio' => $numeroOficio,
            'coordinacion_id' => $coordinacionId,
            'numero' => $numero,
            'anio' => $anioHistorico,
        ];

        return true;
    }

    private function actualizarConsecutivoPorReservado(
        int $coordinacionId,
        int $anio,
        int $numero
    ): void {
        $consecutivo = ConsecutivoOficio::where(
            'coordinacion_id',
            $coordinacionId
        )
        ->where('anio', $anio)
        ->lockForUpdate()
        ->first();

        if (!$consecutivo) {
            ConsecutivoOficio::create([
                'coordinacion_id' => $coordinacionId,
                'anio' => $anio,
                'ultimo_numero' => $numero,
            ]);

            return;
        }

        if ($numero > $consecutivo->ultimo_numero) {
            $consecutivo->update([
                'ultimo_numero' => $numero,
            ]);
        }
    }

    private function obtenerAnioDelNumero(
        string $numeroOficio
    ): ?int {
        if (
            preg_match(
                '/-(\d{4})$/',
                trim($numeroOficio),
                $matches
            )
        ) {
            return (int) $matches[1];
        }

        return null;
    }

    private function esSinNumero(
        string $numeroOficio
    ): bool {
        $numeroOficio = mb_strtoupper(
            preg_replace(
                '/\s+/u',
                '',
                trim($numeroOficio)
            )
        );

        return in_array(
            $numeroOficio,
            [
                'S/N',
                'SINNUMERO',
                'S/NUMERO',
                'S/NUM.',
            ],
            true
        );
    }

    private function descartar(
        array &$resultado,
        string $archivo,
        int $fila,
        ?string $numero,
        string $motivo
    ): void {
        $resultado['estadisticas'][$archivo]['descartados']++;

        $registro = [
            'archivo' => $archivo,
            'fila' => $fila,
            'numero' => $numero,
            'motivo' => $motivo,
        ];

        if ($numero === null) {
            $resultado['descartados_sin_numero'][] = $registro;
        } else {
            $resultado['descartados_con_numero'][] = $registro;
        }
    }

    private function normalizarHeaders(
        array $headers,
        int $tipoOficio
    ): array {
        $headersNormalizados = [];

        foreach ($headers as $index => $header) {
            $header = preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                $header
            );

            $header = trim(
                mb_strtoupper($header)
            );

            if ($tipoOficio === self::TIPO_RECIBIDO) {
                $headersNormalizados[] = match ($index) {
                    0 => 'numero_oficio',
                    1 => 'fecha_oficio',
                    2 => 'fecha_recepcion',
                    3 => 'asunto',
                    4 => 'remitente_nombre',
                    5 => 'remitente_cargo',
                    6 => 'remitente_dependencia',
                    7 => 'destinatario_nombre',
                    8 => 'destinatario_cargo',
                    9 => 'destinatario_dependencia',
                    10 => 'link',
                    default => strtolower($header),
                };

                continue;
            }

            $headersNormalizados[] = match ($header) {
                'FECHA' => 'fecha_oficio',
                'FECHA DE RECIBIDO' => 'fecha_recepcion',

                'COORDINACIÓN' => 'coordinacion',
                'COORDINACION' => 'coordinacion',

                'NÚMERO DE OFICIO' => 'numero_oficio',
                'NUMERO DE OFICIO' => 'numero_oficio',

                'ASUNTO' => 'asunto',

                'REMITENTE' => 'remitente_nombre',
                'CARGO REMITENTE' => 'remitente_cargo',
                'DEPENDENCIA REMITENTE' => 'remitente_dependencia',

                'QUIÉN ELABORA' => 'quien_elabora_nombre',
                'QUIEN ELABORA' => 'quien_elabora_nombre',

                'CARGO QUIEN ELABORA' => 'quien_elabora_cargo',

                'DESTINATARIO' => 'destinatario_nombre',
                'CARGO DESTINADO' => 'destinatario_cargo',
                'DEPENDENCIA' => 'destinatario_dependencia',

                'LINK' => 'link',

                default => strtolower($header),
            };
        }

        return $headersNormalizados;
    }

    private function crearRegistroDesdeFila(
        array $headers,
        array $row
    ): array {
        $row = array_pad(
            $row,
            count($headers),
            null
        );

        $registro = [];

        foreach ($headers as $index => $header) {
            $registro[$header] = $row[$index] ?? null;
        }

        return $registro;
    }

    private function filaVacia(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function esInicializacion(
        string $numeroOficio
    ): bool {
        return (bool) preg_match(
            '/^SESEA-[^-]+-000-\d{4}$/i',
            $numeroOficio
        );
    }

    private function obtenerConsecutivo(
        string $numeroOficio,
        int $tipoOficio
    ): ?string {
        if ($tipoOficio !== self::TIPO_ENVIADO) {
            return null;
        }

        if (
            preg_match(
                '/^SESEA-.+-(\d+)-\d{4}$/i',
                $numeroOficio,
                $matches
            )
        ) {
            return $matches[1];
        }

        return null;
    }

    private function normalizarFecha(
        ?string $fecha
    ): ?string {
        $fecha = trim((string) $fecha);

        if ($fecha === '') {
            return null;
        }

        $formatos = [
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
            'j/n/Y',
            'j-n-Y',
        ];

        foreach ($formatos as $formato) {
            try {
                $fechaCarbon = Carbon::createFromFormat(
                    $formato,
                    $fecha
                );

                if ($fechaCarbon->year < 1900) {
                    return null;
                }

                return $fechaCarbon->format('Y-m-d');

            } catch (\Throwable) {
            }
        }

        if (is_numeric($fecha)) {
            try {
                $fechaCarbon = Carbon::create(
                    1899,
                    12,
                    30
                )->addDays(
                    (int) $fecha
                );

                if ($fechaCarbon->year < 1900) {
                    return null;
                }

                return $fechaCarbon->format('Y-m-d');

            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function extraerUrl(
        ?string $valor
    ): ?string {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        if (
            preg_match(
                '/https?:\/\/[^\s\]\)]+/i',
                $valor,
                $matches
            )
        ) {
            return $matches[0];
        }

        return null;
    }

    private function limpiar(
        ?string $valor
    ): ?string {
        $valor = trim((string) $valor);

        return $valor !== ''
            ? $valor
            : null;
    }

    public function mostrarResultado(
        array $resultado,
        $output
    ): void {
        $output->newLine();

        $output->info(
            '======================================'
        );

        $output->info(
            '       IMPORTACIÓN COMPLETADA'
        );

        $output->info(
            '======================================'
        );

        foreach (
            $resultado['estadisticas']
            as $tipo => $datos
        ) {
            $output->newLine();

            $output->line($tipo);

            $output->line(
                "  Leídos      : {$datos['leidos']}"
            );

            $output->line(
                "  Importados  : {$datos['importados']}"
            );

            $output->line(
                "  Duplicados  : {$datos['duplicados']}"
            );

            $output->line(
                "  Descartados : {$datos['descartados']}"
            );

            if ($datos['reservados'] > 0) {
                $output->line(
                    "  Reservados  : {$datos['reservados']}"
                );
            }

            if ($datos['reservados_existentes'] > 0) {
                $output->line(
                    "  Ya reservados: {$datos['reservados_existentes']}"
                );
            }
        }

        if (!empty($resultado['reservados'])) {
            $output->newLine();

            $output->info(
                'FOLIOS RESERVADOS'
            );

            foreach (
                $resultado['reservados']
                as $reservado
            ) {
                $output->line(
                    "{$reservado['numero_oficio']} — " .
                    "Reservado para {$reservado['anio']}"
                );
            }
        }

        $this->mostrarDescartados(
            $resultado,
            $output
        );
    }

    private function mostrarDescartados(
        array $resultado,
        $output
    ): void {
        $conNumero =
            $resultado['descartados_con_numero'];

        $sinNumero =
            $resultado['descartados_sin_numero'];

        if (
            empty($conNumero) &&
            empty($sinNumero)
        ) {
            $output->newLine();

            $output->info(
                'No hubo registros descartados.'
            );

            return;
        }

        if (!empty($conNumero)) {
            $output->newLine();

            $output->warn(
                '======================================'
            );

            $output->warn(
                '      OFICIOS NO IMPORTADOS'
            );

            $output->warn(
                '======================================'
            );

            foreach ($conNumero as $omitido) {
                $output->line(
                    "{$omitido['numero']} — " .
                    "{$omitido['motivo']}"
                );
            }
        }

        if (!empty($sinNumero)) {
            $output->newLine();

            $output->warn(
                'Registros descartados sin número: ' .
                count($sinNumero)
            );
        }
    }
}
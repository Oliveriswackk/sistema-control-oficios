<?php

namespace App\Console\Commands;

use App\Models\Coordinacion;
use App\Models\Oficio;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportarOficios extends Command
{
    protected $signature = 'app:importar-oficios';

    protected $description = 'Importa oficios históricos desde los CSV de enviados y recibidos';

    private const USUARIO_MIGRACION = 1;

    private const TIPO_ENVIADO = 1;
    private const TIPO_RECIBIDO = 2;

    private const ESTADO_CERRADO = 5;

    public function handle()
    {
        $archivos = [
            [
                'ruta' => storage_path('app/import/oficios_enviados.csv'),
                'tipo' => self::TIPO_ENVIADO,
                'nombre' => 'Enviados',
            ],
            [
                'ruta' => storage_path('app/import/oficios_recibidos.csv'),
                'tipo' => self::TIPO_RECIBIDO,
                'nombre' => 'Recibidos',
            ],
        ];

        foreach ($archivos as $archivo) {
            if (!file_exists($archivo['ruta'])) {
                $this->error("No se encontró el archivo de {$archivo['nombre']}:");
                $this->error($archivo['ruta']);

                return Command::FAILURE;
            }
        }

        $this->info('Iniciando importación histórica...');
        $this->newLine();

        $estadisticas = [
            'Enviados' => [
                'importados' => 0,
                'duplicados' => 0,
                'ignorados' => 0,
            ],
            'Recibidos' => [
                'importados' => 0,
                'duplicados' => 0,
                'ignorados' => 0,
            ],
        ];

        $omitidos = [];

        DB::beginTransaction();

        try {
            foreach ($archivos as $archivo) {
                $this->info("Procesando {$archivo['nombre']}...");

                $this->importarArchivo(
                    $archivo['ruta'],
                    $archivo['tipo'],
                    $archivo['nombre'],
                    $estadisticas[$archivo['nombre']],
                    $omitidos
                );

                $this->newLine();
            }

            DB::commit();

            $this->mostrarResumen($estadisticas);
            $this->mostrarOmitidos($omitidos);

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->newLine();
            $this->error('La importación fue cancelada.');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    private function importarArchivo(
        string $ruta,
        int $tipoOficio,
        string $nombre,
        array &$estadisticas,
        array &$omitidos
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

            if ($this->filaVacia($row)) {
                $estadisticas['ignorados']++;

                $omitidos[] = [
                    'archivo' => $nombre,
                    'fila' => $numeroFila,
                    'numero' => null,
                    'motivo' => 'Fila vacía',
                ];

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
                $estadisticas['ignorados']++;

                $omitidos[] = [
                    'archivo' => $nombre,
                    'fila' => $numeroFila,
                    'numero' => null,
                    'motivo' => 'Sin número de oficio',
                ];

                continue;
            }

            if ($this->esInicializacion($numeroOficio)) {
                $estadisticas['ignorados']++;

                $omitidos[] = [
                    'archivo' => $nombre,
                    'fila' => $numeroFila,
                    'numero' => $numeroOficio,
                    'motivo' => 'Registro 000 / inicialización',
                ];

                continue;
            }

            if (
                Oficio::where(
                    'numero_oficio',
                    $numeroOficio
                )->exists()
            ) {
                $estadisticas['duplicados']++;

                $omitidos[] = [
                    'archivo' => $nombre,
                    'fila' => $numeroFila,
                    'numero' => $numeroOficio,
                    'motivo' => 'Oficio ya existente',
                ];

                continue;
            }

            $fechaOficio = $this->normalizarFecha(
                $data['fecha_oficio'] ?? null
            );

            if (!$fechaOficio) {
                $estadisticas['ignorados']++;

                $omitidos[] = [
                    'archivo' => $nombre,
                    'fila' => $numeroFila,
                    'numero' => $numeroOficio,
                    'motivo' => 'Sin fecha de oficio válida',
                ];

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

            Oficio::create([
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

                'usuario_registro_id' => self::USUARIO_MIGRACION,

                'link_documento' =>
                    $this->extraerUrl(
                        $data['link'] ?? null
                    ),

                'respondido_en' => null,
                'cerrado_en' => null,
                'cancelado_en' => null,
            ]);

            $estadisticas['importados']++;
        }

        fclose($handle);

        $this->info(
            "  {$nombre}: {$estadisticas['importados']} importados, " .
            "{$estadisticas['duplicados']} duplicados, " .
            "{$estadisticas['ignorados']} ignorados."
        );
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
                return Carbon::createFromFormat(
                    $formato,
                    $fecha
                )->format('Y-m-d');
            } catch (\Throwable) {
            }
        }

        if (is_numeric($fecha)) {
            try {
                return Carbon::create(
                    1899,
                    12,
                    30
                )->addDays(
                    (int) $fecha
                )->format('Y-m-d');
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

    private function mostrarResumen(
        array $estadisticas
    ): void {
        $this->newLine();

        $this->info(
            '======================================'
        );

        $this->info(
            '      IMPORTACIÓN COMPLETADA'
        );

        $this->info(
            '======================================'
        );

        foreach ($estadisticas as $tipo => $datos) {
            $this->newLine();

            $this->line($tipo);
            $this->line(
                "  Importados : {$datos['importados']}"
            );
            $this->line(
                "  Duplicados : {$datos['duplicados']}"
            );
            $this->line(
                "  Ignorados  : {$datos['ignorados']}"
            );
        }
    }

    private function mostrarOmitidos(
        array $omitidos
    ): void {
        if (empty($omitidos)) {
            $this->newLine();
            $this->info(
                'No hubo registros omitidos.'
            );

            return;
        }

        $this->newLine();

        $this->warn(
            '======================================'
        );

        $this->warn(
            '       REGISTROS OMITIDOS'
        );

        $this->warn(
            '======================================'
        );

        foreach ($omitidos as $omitido) {
            $numero = $omitido['numero'] ?? 'SIN NÚMERO';

            $this->line(
                "{$omitido['archivo']} | " .
                "Fila {$omitido['fila']} | " .
                "{$numero} | " .
                "{$omitido['motivo']}"
            );
        }

        $this->newLine();

        $this->warn(
            'Total omitidos: ' . count($omitidos)
        );
    }
}
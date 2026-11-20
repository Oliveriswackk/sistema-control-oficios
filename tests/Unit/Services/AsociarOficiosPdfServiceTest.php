<?php

namespace Tests\Unit\Services;

use App\Services\Historico\AsociarOficiosPdfService;
use App\Console\Commands\AsociarOficiosPdf;
use App\Console\Commands\MigrarHistorico;
use App\Models\Oficio;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class AsociarOficiosPdfServiceTest extends TestCase
{
    public function test_normalizes_only_case_and_separators(): void
    {
        $service = new AsociarOficiosPdfService();

        $this->assertSame(
            'OIC418',
            $this->invoke($service, 'normalizarNumero', 'OIC-418')
        );

        $this->assertSame(
            'OIC418',
            $this->invoke($service, 'normalizarNumero', 'OIC.418')
        );

        $this->assertSame(
            'HIE5382',
            $this->invoke($service, 'normalizarNumero', 'hie/5382')
        );

        $this->assertSame(
            'OIC0418',
            $this->invoke($service, 'normalizarNumero', 'OIC-0418')
        );

        $this->assertNotSame(
            $this->invoke($service, 'normalizarNumero', 'OIC-0418'),
            $this->invoke($service, 'normalizarNumero', 'OIC-418')
        );
    }

    public function test_extracts_sent_number_before_description(): void
    {
        $service = new AsociarOficiosPdfService();

        $this->assertSame(
            'SESEA-CA-002-2026',
            $this->invoke(
                $service,
                'obtenerNumeroDesdePdf',
                'SESEA-CA-002-2026-MOVIMIENTO-DE-PERSONAL.pdf',
                'Enviados'
            )
        );
    }

    public function test_received_date_is_optional_and_only_eight_digit_prefix_is_removed(): void
    {
        $service = new AsociarOficiosPdfService();

        $this->assertSame(
            '-HIE-5382',
            $receivedWithDate = $this->invoke(
                $service,
                'obtenerNumeroDesdePdf',
                '20260112-HIE-5382.pdf',
                'Recibidos'
            )
        );

        $this->assertSame(
            'HIE5382',
            $this->invoke(
                $service,
                'normalizarNumero',
                $receivedWithDate
            )
        );

        $this->assertSame(
            'HIE-5382',
            $this->invoke(
                $service,
                'obtenerNumeroDesdePdf',
                'HIE-5382.pdf',
                'Recibidos'
            )
        );

        $this->assertSame(
            'SFP-OIC-SG-SESEA-02-2026-ANEXO-3',
            $this->invoke(
                $service,
                'obtenerNumeroDesdePdf',
                '20260203SFP-OIC-SG-SESEA-02-2026-ANEXO-3.pdf',
                'Recibidos'
            )
        );
    }

    public function test_search_is_limited_to_the_directory_type(): void
    {
        $service = new AsociarOficiosPdfService();
        $enviado = new Oficio();
        $enviado->id = 1;
        $recibido = new Oficio();
        $recibido->id = 2;

        $indice = [
            1 => ['OIC418' => [$enviado]],
            2 => ['OIC418' => [$recibido]],
        ];

        $this->assertSame(
            $enviado,
            $this->invoke(
                $service,
                'buscarOficioNormalizado',
                'OIC-418',
                $indice,
                [1]
            )
        );

        $this->assertSame(
            $recibido,
            $this->invoke(
                $service,
                'buscarOficioNormalizado',
                'OIC.418',
                $indice,
                [2]
            )
        );
    }

    public function test_search_finds_received_number_inside_descriptive_name(): void
    {
        $service = new AsociarOficiosPdfService();
        $oficio = new Oficio();
        $oficio->id = 1;

        $this->assertSame(
            $oficio,
            $this->invoke(
                $service,
                'buscarOficioNormalizado',
                'DOCUMENTACION-RECIBIDA-OIC.418-ANEXOS',
                [
                    2 => [
                        'OIC418' => [$oficio],
                    ],
                ],
                [2]
            )
        );
    }

    public function test_search_does_not_match_partial_or_zero_variants(): void
    {
        $service = new AsociarOficiosPdfService();
        $oficio = new Oficio();
        $oficio->id = 1;

        $indice = [
            2 => [
                'OIC418' => [$oficio],
            ],
        ];

        $this->assertNull(
            $this->invoke(
                $service,
                'buscarOficioNormalizado',
                'DOCUMENTACION-OIC-0418-ANEXOS',
                $indice,
                [2]
            )
        );

        $this->assertNull(
            $this->invoke(
                $service,
                'buscarOficioNormalizado',
                'DOCUMENTACION-OIC-41-ANEXOS',
                $indice,
                [2]
            )
        );
    }

    public function test_search_rejects_multiple_matches_as_ambiguous(): void
    {
        $service = new AsociarOficiosPdfService();
        $oficioUno = new Oficio();
        $oficioUno->id = 1;
        $oficioDos = new Oficio();
        $oficioDos->id = 2;

        $this->expectException(\RuntimeException::class);

        $this->invoke(
            $service,
            'buscarOficioNormalizado',
            'OIC-418',
            [
                2 => [
                    'OIC418' => [$oficioUno, $oficioDos],
                ],
            ],
            [2]
        );
    }

    public function test_detects_generic_no_number_identifiers_without_matching_database(): void
    {
        $service = new AsociarOficiosPdfService();

        $this->assertTrue(
            $this->invoke(
                $service,
                'esNumeroSinNumero',
                '20260127-S-N-ANEXOS'
            )
        );

        $this->assertTrue(
            $this->invoke(
                $service,
                'esNumeroSinNumero',
                'DOCUMENTACION-SIN NUMERO'
            )
        );

        $this->assertTrue(
            $this->invoke(
                $service,
                'esNumeroSinNumero',
                'DOCUMENTACION-SIN NÚMERO'
            )
        );

        $this->assertFalse(
            $this->invoke(
                $service,
                'esNumeroSinNumero',
                'DOCUMENTACION-SN123'
            )
        );
    }

    public function test_both_commands_report_the_same_result_details(): void
    {
        $detalle = [
            [
                'archivo' => 'OIC-418-ANEXOS.pdf',
                'numero' => 'OIC-418',
                'oficio_id' => 42,
                'motivo' => 'El oficio ya tiene un documento asociado',
            ],
        ];

        foreach ([AsociarOficiosPdf::class, MigrarHistorico::class] as $clase) {
            $comando = new $clase();
            $buffer = new BufferedOutput();
            $salida = new OutputStyle(
                new ArrayInput([]),
                $buffer
            );
            $comando->setOutput($salida);

            $reflection = new ReflectionMethod(
                $comando,
                'mostrarDetalles'
            );

            $reflection->invoke(
                $comando,
                'PDFs YA EXISTENTES',
                $detalle,
                'warn'
            );

            $texto = $buffer->fetch();

            $this->assertStringContainsString(
                'OIC-418-ANEXOS.pdf',
                $texto
            );
            $this->assertStringContainsString(
                'OIC-418 | ID 42',
                $texto
            );
            $this->assertStringContainsString(
                'ya tiene un documento asociado',
                $texto
            );
        }
    }

    public function test_individual_command_reports_categories_and_summary_in_order(): void
    {
        $comando = new AsociarOficiosPdf();
        $buffer = new BufferedOutput();
        $comando->setOutput(
            new OutputStyle(new ArrayInput([]), $buffer)
        );

        $resultado = [
            'encontrados' => 7,
            'asociados' => 1,
            'ya_existentes' => 1,
            'ya_existentes_detalle' => [
                [
                    'archivo' => 'existente.pdf',
                    'numero' => 'OIC-418',
                    'oficio_id' => 42,
                    'motivo' => 'No se sobrescribió',
                ],
            ],
            'asociados_detalle' => [
                [
                    'archivo' => 'asociado.pdf',
                    'numero' => 'OIC-419',
                    'oficio_id' => 43,
                    'motivo' => 'PDF asociado correctamente',
                ],
            ],
            'oficios_sin_pdf' => [
                [
                    'id' => 44,
                    'numero' => 'OIC-420',
                    'tipo_oficio_id' => 2,
                    'tipo' => 'Recibido',
                    'motivo' => 'No se encontró un PDF correspondiente',
                ],
            ],
            'sin_oficio' => [
                [
                    'archivo' => 'sin-oficio.pdf',
                    'numero' => '-SSE-003-2026',
                    'oficio_id' => null,
                    'motivo' => 'No existe un oficio correspondiente',
                ],
            ],
            'ambiguos' => [
                [
                    'archivo' => 'ambiguo.pdf',
                    'numero' => 'SSE-107-2026',
                    'oficio_id' => null,
                    'motivo' => 'Candidatos: ID 1 (SSE-107-2026), ID 2 (107-2026)',
                ],
            ],
            'errores' => 1,
            'errores_detalle' => [
                [
                    'archivo' => 'fallido.pdf',
                    'numero' => 'OIC-421',
                    'oficio_id' => 45,
                    'etapa' => 'almacenar PDF',
                    'motivo' => 'No fue posible guardar el PDF',
                ],
            ],
            'ignorados_detalle' => [],
            'sin_numero' => [
                [
                    'archivo' => '20260127-S-N.pdf',
                    'ruta' => 'C:\\historico\\20260127-S-N.pdf',
                ],
            ],
        ];

        $reflection = new ReflectionMethod(
            $comando,
            'mostrarResultado'
        );
        $reflection->invoke($comando, $resultado);
        $texto = $buffer->fetch();

        $this->assertLessThan(
            strpos($texto, 'PDFs YA EXISTENTES'),
            strpos($texto, 'PDFs ASOCIADOS')
        );
        $this->assertLessThan(
            strpos($texto, 'OFICIOS SIN PDF'),
            strpos($texto, 'PDFs YA EXISTENTES')
        );
        $this->assertLessThan(
            strpos($texto, 'PDFs SIN OFICIO'),
            strpos($texto, 'OFICIOS SIN PDF')
        );
        $this->assertLessThan(
            strpos($texto, 'PDFs AMBIGUOS'),
            strpos($texto, 'PDFs SIN OFICIO')
        );
        $this->assertLessThan(
            strpos($texto, 'RESUMEN'),
            strpos($texto, 'PDFs SIN NÚMERO')
        );
        $this->assertStringContainsString(
            'ID 44 | OIC-420 | Tipo 2 (Recibido)',
            $texto
        );
        $this->assertStringContainsString(
            'sin-oficio.pdf | SSE-003-2026',
            $texto
        );
        $this->assertStringNotContainsString(
            'sin-oficio.pdf | -SSE-003-2026',
            $texto
        );
        $this->assertStringContainsString(
            'PDFs sin número (S-N): 1',
            $texto
        );
        $this->assertStringContainsString(
            'Etapa: almacenar PDF',
            $texto
        );
        $this->assertStringContainsString(
            'Estado: COMPLETADO CON INCIDENCIAS',
            $texto
        );
        $this->assertStringContainsString(
            'ID 42 | OIC-418 | PDF omitido: existente.pdf',
            $texto
        );
        $this->assertStringContainsString(
            'ID 43 | OIC-419 | asociado.pdf',
            $texto
        );
    }

    public function test_migration_command_uses_same_states_and_summary(): void
    {
        $comando = new MigrarHistorico();
        $buffer = new BufferedOutput();
        $comando->setOutput(
            new OutputStyle(new ArrayInput([]), $buffer)
        );

        $resultado = [
            'encontrados' => 4,
            'asociados' => 1,
            'ya_existentes' => 1,
            'ya_existentes_detalle' => [
                [
                    'archivo' => 'existente-migracion.pdf',
                    'numero' => 'OIC-418',
                    'oficio_id' => 142,
                    'motivo' => 'El oficio ya tiene un documento asociado',
                ],
            ],
            'asociados_detalle' => [
                [
                    'archivo' => 'asociado-migracion.pdf',
                    'numero' => 'OIC-419',
                    'oficio_id' => 143,
                    'motivo' => 'PDF asociado correctamente',
                ],
            ],
            'oficios_sin_pdf' => [
                [
                    'id' => 144,
                    'numero' => 'OIC-420',
                    'tipo_oficio_id' => 2,
                    'tipo' => 'Recibido',
                    'motivo' => 'No se encontró un PDF correspondiente',
                ],
            ],
            'sin_oficio' => [
                [
                    'archivo' => 'sin-oficio-migracion.pdf',
                    'numero' => 'OIC-999',
                    'oficio_id' => null,
                    'motivo' => 'No existe un oficio correspondiente',
                ],
            ],
            'ambiguos' => [],
            'errores' => 0,
            'errores_detalle' => [],
            'ignorados_detalle' => [],
            'sin_numero' => [],
        ];

        $mostrarPdf = new ReflectionMethod($comando, 'mostrarPdf');
        $mostrarPdf->invoke($comando, $resultado);

        $mostrarFinal = new ReflectionMethod($comando, 'mostrarFinal');
        $mostrarFinal->invoke($comando, $resultado);

        $texto = $buffer->fetch();

        $this->assertStringContainsString(
            'PDFs ASOCIADOS (1)',
            $texto
        );
        $this->assertStringContainsString(
            'ID 143 | OIC-419 | asociado-migracion.pdf',
            $texto
        );
        $this->assertStringContainsString(
            'PDFs YA EXISTENTES (1)',
            $texto
        );
        $this->assertStringContainsString(
            'ID 142 | OIC-418 | PDF omitido: existente-migracion.pdf',
            $texto
        );
        $this->assertStringContainsString(
            'OFICIOS SIN PDF (1)',
            $texto
        );
        $this->assertStringContainsString(
            'ID 144 | OIC-420 | Tipo 2 (Recibido)',
            $texto
        );
        $this->assertStringContainsString(
            'PDFs SIN OFICIO (1)',
            $texto
        );
        $this->assertStringContainsString(
            'sin-oficio-migracion.pdf | OIC-999',
            $texto
        );
        $this->assertSame(
            1,
            substr_count($texto, 'ID 143 | OIC-419 | asociado-migracion.pdf')
        );
        $this->assertSame(
            1,
            substr_count($texto, 'ID 142 | OIC-418 | PDF omitido: existente-migracion.pdf')
        );
        $this->assertSame(
            1,
            substr_count($texto, 'ID 144 | OIC-420 | Tipo 2 (Recibido)')
        );
        $this->assertSame(
            1,
            substr_count($texto, 'sin-oficio-migracion.pdf | OIC-999')
        );
        $this->assertStringContainsString('RESUMEN', $texto);
        $this->assertStringContainsString(
            'PDFs encontrados: 4',
            $texto
        );
        $this->assertStringContainsString(
            'Asociaciones exitosas: 1',
            $texto
        );
        $this->assertStringContainsString(
            'Documentos ya existentes: 1',
            $texto
        );
        $this->assertStringContainsString(
            'Oficios sin PDF: 1',
            $texto
        );
        $this->assertStringContainsString(
            'PDFs sin oficio: 1',
            $texto
        );
        $this->assertStringContainsString(
            'PDFs sin número (S-N): 0',
            $texto
        );
        $this->assertStringNotContainsString(
            'MIGRACIÓN COMPLETADA',
            $texto
        );
    }

    private function invoke(
        object $service,
        string $method,
        mixed ...$arguments
    ): mixed {
        $reflection = new ReflectionMethod(
            $service,
            $method
        );

        return $reflection->invoke(
            $service,
            ...$arguments
        );
    }
}

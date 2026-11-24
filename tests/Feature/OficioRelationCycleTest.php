<?php

namespace Tests\Feature;

use App\Http\Controllers\OficioController;
use App\Http\Requests\StoreOficioRequest;
use App\Models\Oficio;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use ReflectionMethod;
use Tests\TestCase;

class OficioRelationCycleTest extends TestCase
{
    private int $estadoId;
    private int $tipoOficioId;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('estados_oficio')) {
            Schema::create('estados_oficio', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tipos_oficio')) {
            Schema::create('tipos_oficio', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('oficios')) {
            Schema::create('oficios', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('respuesta_a_oficio_id')->nullable();
                $table->unsignedBigInteger('tipo_oficio_id');
                $table->unsignedBigInteger('estado_id');
                $table->string('numero_oficio');
                $table->string('asunto');
                $table->date('fecha_oficio')->nullable();
                $table->dateTime('cerrado_en')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('oficio_historial')) {
            Schema::create('oficio_historial', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('oficio_id');
                $table->unsignedBigInteger('usuario_id')->nullable();
                $table->string('accion');
                $table->text('descripcion')->nullable();
                $table->unsignedBigInteger('estado_anterior_id')->nullable();
                $table->unsignedBigInteger('estado_nuevo_id')->nullable();
                $table->string('entidad_relacionada')->nullable();
                $table->unsignedBigInteger('entidad_relacionada_id')->nullable();
                $table->timestamps();
            });
        }

        $now = now();
        $this->estadoId = DB::table('estados_oficio')->insertGetId([
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->tipoOficioId = DB::table('tipos_oficio')->insertGetId([
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function test_valid_antecedent_does_not_create_a_cycle(): void
    {
        $actual = $this->crearOficio();
        $antecedente = $this->crearOficio();

        $this->assertFalse($this->relacionFormariaCiclo(
            $antecedente->id,
            $actual->id
        ));
    }

    public function test_self_relation_is_rejected(): void
    {
        $oficio = $this->crearOficio();

        $this->assertTrue($this->relacionFormariaCiclo(
            $oficio->id,
            $oficio->id
        ));
    }

    public function test_descendant_antecedent_is_rejected_as_an_indirect_cycle(): void
    {
        $actual = $this->crearOficio();
        $respuesta = $this->crearOficio($actual->id);
        $descendiente = $this->crearOficio($respuesta->id);

        $this->assertTrue($this->relacionFormariaCiclo(
            $descendiente->id,
            $actual->id
        ));
    }

    public function test_nonexistent_antecedent_is_rejected_by_store_validation(): void
    {
        $input = [
            'tipo_relacion' => 'relacionado',
            'respuesta_a_oficio_id' => 999999999,
        ];
        $request = StoreOficioRequest::create('/', 'POST', $input);
        $rules = $request->rules();
        $validator = Validator::make(
            $input,
            array_intersect_key(
                $rules,
                array_flip(['tipo_relacion', 'respuesta_a_oficio_id'])
            )
        );

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey(
            'respuesta_a_oficio_id',
            $validator->errors()->toArray()
        );
    }

    public function test_confirming_new_petition_only_records_the_confirmation(): void
    {
        $oficio = $this->crearOficio();
        $estadoId = $oficio->estado_id;
        $asunto = $oficio->asunto;

        $this->assertFalse($oficio->relacionDefinida());

        $oficio->confirmarRelacionNueva();

        $guardado = $oficio->fresh();
        $this->assertTrue($guardado->relacionDefinida());
        $this->assertNull($guardado->respuesta_a_oficio_id);
        $this->assertSame($estadoId, $guardado->estado_id);
        $this->assertSame($asunto, $guardado->asunto);
        $this->assertSame(
            1,
            $guardado->historial()
                ->where(
                    'accion',
                    Oficio::ACCION_RELACION_NUEVA_CONFIRMADA
                )
                ->count()
        );

        $guardado->confirmarRelacionNueva();
        $this->assertSame(
            1,
            $guardado->historial()
                ->where(
                    'accion',
                    Oficio::ACCION_RELACION_NUEVA_CONFIRMADA
                )
                ->count()
        );
    }

    public function test_trajectory_stops_when_existing_relations_form_a_cycle(): void
    {
        $primero = $this->crearOficio();
        $segundo = $this->crearOficio($primero->id);

        DB::table('oficios')
            ->where('id', $primero->id)
            ->update(['respuesta_a_oficio_id' => $segundo->id]);

        $trayectoria = $this->invocarMetodoPrivado(
            'obtenerTrayectoria',
            [$primero->fresh()]
        );

        $ids = collect($trayectoria['nodos'])
            ->pluck('id')
            ->all();

        $this->assertCount(2, $ids);
        $this->assertCount(2, array_unique($ids));
    }

    private function crearOficio(?int $antecedenteId = null): Oficio
    {
        $now = now();
        $id = DB::table('oficios')->insertGetId([
            'respuesta_a_oficio_id' => $antecedenteId,
            'tipo_oficio_id' => $this->tipoOficioId,
            'estado_id' => $this->estadoId,
            'numero_oficio' => 'PRUEBA-' . uniqid(),
            'asunto' => 'Oficio de prueba',
            'fecha_oficio' => $now->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return Oficio::findOrFail($id);
    }

    private function relacionFormariaCiclo(
        int $antecedenteId,
        int $oficioActualId
    ): bool {
        return $this->invocarMetodoPrivado(
            'relacionFormariaCiclo',
            [$antecedenteId, $oficioActualId]
        );
    }

    private function invocarMetodoPrivado(string $metodo, array $argumentos)
    {
        $reflection = new ReflectionMethod(OficioController::class, $metodo);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs(
            new OficioController(),
            $argumentos
        );
    }
}

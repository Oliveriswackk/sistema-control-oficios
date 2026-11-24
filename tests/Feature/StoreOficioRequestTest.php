<?php

namespace Tests\Feature;

use App\Http\Requests\StoreOficioRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreOficioRequestTest extends TestCase
{
    public function test_drive_link_is_required_only_for_non_sensitive_received_offices(): void
    {
        $this->assertFalse($this->passesRules([
            'tipo_oficio_id' => 2,
            'es_sensible' => 0,
        ], ['link_drive']));

        $this->assertTrue($this->passesRules([
            'tipo_oficio_id' => 2,
            'es_sensible' => 1,
        ], ['link_drive']));

        $this->assertTrue($this->passesRules([
            'tipo_oficio_id' => 1,
            'es_sensible' => 0,
        ], ['link_drive']));
    }

    public function test_relation_is_optional_but_requires_an_id_when_responding_to_an_office(): void
    {
        $this->assertTrue($this->passesRules([], [
            'tipo_relacion',
            'respuesta_a_oficio_id',
        ]));

        $this->assertFalse($this->passesRules([
            'tipo_relacion' => 'relacionado',
        ], [
            'tipo_relacion',
            'respuesta_a_oficio_id',
        ]));
    }

    private function passesRules(array $input, array $fields): bool
    {
        $request = StoreOficioRequest::create('/', 'POST', $input);
        $rules = $request->rules();

        return Validator::make(
            $input,
            array_intersect_key($rules, array_flip($fields))
        )->passes();
    }
}

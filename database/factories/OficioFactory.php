<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Oficio;

class OficioFactory extends Factory
{
    protected $model = Oficio::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),

            'numero_oficio' => fake()->bothify('OF-####'),
            'consecutivo' => fake()->numerify('####'),

            'tipo_oficio_id' => 1,
            'estado_id' => 1,

            'asunto' => fake()->sentence(),
            'descripcion' => fake()->paragraph(),

            'fecha_oficio' => now()->subDays(rand(1, 30)),
            'fecha_recepcion' => now()->subDays(rand(0, 10)),
            'fecha_limite' => now()->addDays(rand(5, 20)),

            'requiere_respuesta' => fake()->boolean(),
            'es_sensible' => fake()->boolean(),

            'remitente_nombre' => fake()->name(),
            'remitente_cargo' => fake()->jobTitle(),
            'remitente_dependencia' => fake()->company(),

            'destinatario_nombre' => fake()->name(),
            'destinatario_cargo' => fake()->jobTitle(),
            'destinatario_dependencia' => fake()->company(),

            'quien_elabora_nombre' => fake()->name(),
            'quien_elabora_cargo' => fake()->jobTitle(),

            'responsable_inicial_id' => 1,
            'coordinacion_origen_id' => 1,
            'usuario_registro_id' => 1,

            'link_documento' => fake()->url(),
        ];
    }
}
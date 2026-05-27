<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Permiso;

class PermisoFactory extends Factory
{
    protected $model = Permiso::class;

    public function definition(): array
    {
        return [
            'clave' => fake()->unique()->slug(),
            'nombre' => fake()->sentence(2),
            'descripcion' => fake()->sentence(),
        ];
    }
}
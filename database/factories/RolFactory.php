<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rol;

class RolFactory extends Factory
{
    protected $model = Rol::class;

    public function definition(): array
    {
        return [
            'clave' => fake()->unique()->slug(),
            'nombre' => fake()->jobTitle(),
            'descripcion' => fake()->sentence(),
            'activo' => true,
        ];
    }
}
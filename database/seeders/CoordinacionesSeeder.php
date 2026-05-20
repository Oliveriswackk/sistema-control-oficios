<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coordinacion;

class CoordinacionesSeeder extends Seeder
{
    public function run(): void
    {
        Coordinacion::insert([
            ['clave' => 'JUR', 'nombre' => 'Jurídico', 'activo' => true],
            ['clave' => 'SIS', 'nombre' => 'Sistemas', 'activo' => true],
            ['clave' => 'RIS', 'nombre' => 'Riesgos', 'activo' => true],
            ['clave' => 'ADM', 'nombre' => 'Administración', 'activo' => true],
            ['clave' => 'DIR', 'nombre' => 'Dirección', 'activo' => true],
            ['clave' => 'VIN', 'nombre' => 'Vinculación', 'activo' => true],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosTurnadoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estados_turnado')->insert([
            [
                'clave' => 'activo',
                'nombre' => 'Activo',
                'activo' => true,
            ],
            [
                'clave' => 'en_atencion',
                'nombre' => 'En atención',
                'activo' => true,
            ],
            [
                'clave' => 'atendido',
                'nombre' => 'Atendido',
                'activo' => true,
            ],
            [
                'clave' => 'cerrado',
                'nombre' => 'Cerrado',
                'activo' => true,
            ],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposParticipacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_participacion')->insert([
            [
                'clave' => 'responsable',
                'nombre' => 'Responsable operativo',
                'implica_responsabilidad' => true,
                'activo' => true,
            ],
            [
                'clave' => 'apoyo',
                'nombre' => 'Apoyo operativo',
                'implica_responsabilidad' => false,
                'activo' => true,
            ],
            [
                'clave' => 'ccp',
                'nombre' => 'C.C.P. — Para conocimiento',
                'implica_responsabilidad' => false,
                'activo' => true,
            ],
        ]);
    }
}
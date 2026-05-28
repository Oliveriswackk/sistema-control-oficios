<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposOficioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_oficio')->insert([
            [
                'clave' => 'enviado',
                'nombre' => 'Enviado',
                'activo' => true,
            ],
            [
                'clave' => 'recibido',
                'nombre' => 'Recibido',
                'activo' => true,
            ],
            [
                'clave' => 'recibido_cpc',
                'nombre' => 'Recibido CPC',
                'activo' => true,
            ],
        ]);
    }
}
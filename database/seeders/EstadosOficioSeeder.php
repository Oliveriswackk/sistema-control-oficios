<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosOficioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estados_oficio')->insert([
            [
                'clave' => 'registrado',
                'nombre' => 'Registrado',
                'color' => '#6c757d',
                'orden' => 1,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'en_seguimiento',
                'nombre' => 'En seguimiento',
                'color' => '#0d6efd',
                'orden' => 2,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'turnado',
                'nombre' => 'Turnado',
                'color' => '#fd7e14',
                'orden' => 3,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'vencido',
                'nombre' => 'Vencido',
                'color' => '#830352',
                'orden' => 4,
                'es_final' => false,
                'activo' => true,
            ],
            [
                'clave' => 'cerrado',
                'nombre' => 'Cerrado',
                'color' => '#198754',
                'orden' => 5,
                'es_final' => true,
                'activo' => true,
            ],
            [
                'clave' => 'cancelado',
                'nombre' => 'Cancelado',
                'color' => '#dc3545',
                'orden' => 6,
                'es_final' => true,
                'activo' => true,
            ],
        ]);
    }
}
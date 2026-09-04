<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coordinacion;

class CoordinacionesSeeder extends Seeder
{
    public function run(): void
    {
        Coordinacion::insert([

        /*      == Coordinaciones Internas == */
            [
                'clave' => 'CJ', // 1
                'nombre' => 'Asuntos Jurídicos',
                'activo' => true,
            ],

            [
                'clave' => 'CC-SEA',
                'nombre' => 'Comité Coordinador del Sistema Estatal Anticorrupción',
                'activo' => true,
            ],
            
            [
                'clave' => 'ETICA', // 3
                'nombre' => 'Comité de Ética',
                'activo' => true,
            ],
                
            [
                'clave' => 'CA', // 4
                'nombre' => 'Coordinación Administrativa',
                'activo' => true,
            ],

            [
                'clave' => 'CRyPP', // 5
                'nombre' => 'Riesgos y Políticas Públicas',
                'activo' => true,
            ],

            [
                'clave' => 'ST', // 6
                'nombre' => 'Secretaría Técnica',
                'activo' => true,
            ],
                
            [
                'clave' => 'CSTyPD', // 7
                'nombre' => 'Servicios Tecnológicos y Plataforma Digital',
                'activo' => true,
            ],
            
            [
                'clave' => 'UIG', // 8
                'nombre' => 'Unidad de Igualdad de Género',
                'activo' => true,
            ],

            [
                'clave' => 'UT', // 9
                'nombre' => 'Unidad de Transparencia',
                'activo' => true,
            ],

            [
                'clave' => 'CVIySC', // 10
                'nombre' => 'Vinculación Institucional y con la Sociedad Civil',
                'activo' => true,
            ],

        ]);
    }
}
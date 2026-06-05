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
                'clave' => 'OP',
                'nombre' => 'Oficialía de Partes',
                'activo' => true,
            ],

            [
                'clave' => 'CS',
                'nombre' => 'Oficina del C. Secretario',
                'activo' => true,
            ],

            [
                'clave' => 'VIN',
                'nombre' => 'Vinculación',
                'activo' => true,
            ],

            [
                'clave' => 'RIS',
                'nombre' => 'Riesgos y Políticas Públicas',
                'activo' => true,
            ],

            [
                'clave' => 'JUR',
                'nombre' => 'Asuntos Jurídicos',
                'activo' => true,
            ],
            
            [
                'clave' => 'SIS',
                'nombre' => 'Servicios Tecnológicos',
                'activo' => true,
            ],
            
            [
                'clave' => 'CA', 
                'nombre' => 'Coordinación Administrativa',
                'activo' => true,
            ],

            /* 
                    == Envio de oficios ==
            [
                'clave' => 'ST',
                'nombre' => 'Secretaría Técnica',
                'activo' => true,
            ],

            [
                'clave' => 'UIG',
                'nombre' => 'Unidad de Igualdad de Género',
                'activo' => true,
            ],

            [
                'clave' => 'CVIYSC',
                'nombre' => 'Coordinación de Vinculación Interinstitucional y de la Sociedad Civil',
                'activo' => true,
            ],

            [
                'clave' => 'CC-SEA',
                'nombre' => 'Comité Coordinador del Sistema Estatal Anticorrupción',
                'activo' => true,
            ],
            */
        ]);
    }
}
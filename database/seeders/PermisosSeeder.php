<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            [
                'clave' => 'puede_registrar_oficios',
                'nombre' => 'Registrar oficios',
                'descripcion' => 'Permite crear oficios en el sistema',
            ],
            [
                'clave' => 'puede_turnar',
                'nombre' => 'Turnar oficios',
                'descripcion' => 'Permite asignar oficios a coordinaciones o usuarios',
            ],
            [
                'clave' => 'puede_cerrar',
                'nombre' => 'Cerrar oficios',
                'descripcion' => 'Permite cerrar oficios',
            ],
            [
                'clave' => 'puede_returnar',
                'nombre' => 'Re-turnar oficios',
                'descripcion' => 'Permite reasignar turnados',
            ],
            [
                'clave' => 'puede_ver_sensibles',
                'nombre' => 'Ver información sensible',
                'descripcion' => 'Acceso a documentos sensibles del sistema',
            ],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['clave' => $permiso['clave']],
                $permiso
            );
        }
    }
}
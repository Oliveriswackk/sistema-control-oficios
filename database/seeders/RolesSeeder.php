<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'clave' => 'admin',
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'activo' => true,
            ],
            [
                'clave' => 'colaborador',
                'nombre' => 'Colaborador',
                'descripcion' => 'Registro y seguimiento de oficios',
                'activo' => true,
            ],
            [
                'clave' => 'consulta',
                'nombre' => 'Consulta',
                'descripcion' => 'Solo lectura de información',
                'activo' => true,
            ],
        ];

        foreach ($roles as $role) {
            Rol::updateOrCreate(
                ['clave' => $role['clave']],
                $role
            );
        }
    }
}
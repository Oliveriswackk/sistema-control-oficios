<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Rol::where('clave', 'admin')->firstOrFail();
        $colaborador = Rol::where('clave', 'colaborador')->firstOrFail();
        $consulta = Rol::where('clave', 'consulta')->firstOrFail();

        $permisos = Permiso::all()->keyBy('clave');

        // ADMIN → todos los permisos
        $admin->permisos()->sync(
            $permisos->pluck('id')->toArray()
        );

        // COLABORADOR → Permisos pasan a ser individuales
        $colaborador->permisos()->sync([]);

        // CONSULTA → sin permisos operativos
        $consulta->permisos()->sync([]);
    }
}
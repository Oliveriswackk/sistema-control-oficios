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
        $coordinador = Rol::where('clave', 'coordinador')->firstOrFail();
        $colaborador = Rol::where('clave', 'colaborador')->firstOrFail();
        $consulta = Rol::where('clave', 'consulta')->firstOrFail();

        $permisos = Permiso::all()->keyBy('clave');

        // ADMIN → todos los permisos
        $admin->permisos()->sync(
            $permisos->pluck('id')->toArray()
        );

        // COORDINADOR → permisos individuales
        $coordinador->permisos()->sync([]);

        // COLABORADOR → permisos individuales
        $colaborador->permisos()->sync([]);

        // CONSULTA → sin permisos operativos
        $consulta->permisos()->sync([]);
    }
}

        

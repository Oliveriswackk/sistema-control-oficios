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
        $captura = Rol::where('clave', 'captura')->firstOrFail();
        $consulta = Rol::where('clave', 'consulta')->firstOrFail();

        $permisos = Permiso::all()->keyBy('clave');

        // ADMIN → todos los permisos
        $admin->permisos()->sync(
            $permisos->pluck('id')->toArray()
        );

        // CAPTURA → operación base (limpiar nulls por seguridad)
        $captura->permisos()->sync(
            collect([
                $permisos['puede_registrar_oficios']->id ?? null,
                $permisos['puede_turnar']->id ?? null,
            ])->filter()->values()->toArray()
        );

        // CONSULTA → sin permisos operativos
        $consulta->permisos()->sync([]);
    }
}
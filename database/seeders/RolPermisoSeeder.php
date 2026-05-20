<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Rol::where('clave', 'admin')->first();
        $captura = Rol::where('clave', 'captura')->first();
        $consulta = Rol::where('clave', 'consulta')->first();

        $permisos = Permiso::all()->keyBy('clave');

        // ADMIN → todo
        $admin->permisos()->sync($permisos->pluck('id'));

        // CAPTURA → operar
        $captura->permisos()->sync([
            $permisos['puede_registrar_oficios']->id,
            $permisos['puede_turnar']->id,
        ]);

        // CONSULTA → solo lectura (si agregas luego permiso de ver)
        $consulta->permisos()->sync([]);
    }
}
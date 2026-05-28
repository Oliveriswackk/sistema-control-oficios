<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CoordinacionesSeeder::class,

            // 1. Seguridad base
            RolesSeeder::class,
            PermisosSeeder::class,
            RolPermisoSeeder::class,

            // 2. Usuarios
            UsersSeeder::class,

            // 3. Catalogos
            TiposOficioSeeder::class,
            EstadosOficioSeeder::class,
            TiposParticipacionSeeder::class,
            EstadosTurnadoSeeder::class,
        ]);
    }
}
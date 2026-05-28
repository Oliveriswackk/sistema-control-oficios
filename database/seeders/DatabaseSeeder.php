<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CoordinacionesSeeder::class,

            // 1. Catalogos
            TiposOficioSeeder::class,
            EstadosOficioSeeder::class,
        
            // 2. seguridad base
            RolesSeeder::class,
            PermisosSeeder::class,
            RolPermisoSeeder::class,

            // 3. Usuarios
            UsersSeeder::class,
        ]);
    }
}
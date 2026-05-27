<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CoordinacionesSeeder::class,

            // 1. seguridad base
            RolesSeeder::class,
            PermisosSeeder::class,
            RolPermisoSeeder::class,

            // 2. usuarios al final
            UsersSeeder::class,
        ]);
    }
}
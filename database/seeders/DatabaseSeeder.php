<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CoordinacionesSeeder::class,
            UsersSeeder::class,
            RolesSeeder::class,
            PermisosSeeder::class,
            RolPermisoSeeder::class,
        ]);
    }
}
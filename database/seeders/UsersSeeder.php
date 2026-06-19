<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Rol::where('clave', 'admin')->first();
        $capturaRole = Rol::where('clave', 'captura')->first();

        $permisoCerrar = Permiso::where('clave', 'puede_cerrar')->first();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        $admin = User::create([
            'name' => 'Oliver Coronado',
            'email' => 'admin@sesea.test',
            'password' => Hash::make('Oliwey777'),
        ]);

        $admin->roles()->attach($adminRole->id);

        $admin->coordinaciones()->attach([
            6
        ]);

        /*
        |--------------------------------------------------------------------------
        | RECEPCIÓN
        |--------------------------------------------------------------------------
        */
        $recepcion = User::create([
            'name' => 'Daniela Ruiz',
            'email' => 'recepcion@sesea.test',
            'password' => Hash::make('123456789'),
        ]);

        $recepcion->roles()->attach($capturaRole->id);

        $permisoCerrar = Permiso::where('clave', 'puede_cerrar')->first();
        $permisoRegistrar = Permiso::where('clave', 'puede_registrar_oficios')->first();

        $recepcion->permisos()->attach([
            $permisoCerrar->id,
            $permisoRegistrar->id,
        ]);

        $recepcion->coordinaciones()->attach([1]);

        /*
        |--------------------------------------------------------------------------
        | JURÍDICO
        |--------------------------------------------------------------------------
        */
        $juridico = User::create([
            'name' => 'Dania Perez',
            'email' => 'juridico@sesea.test',
            'password' => Hash::make('123456789'),
        ]);

        $juridico->roles()->attach($capturaRole->id);

        $juridico->coordinaciones()->attach([
            5
        ]);

        /*
        |--------------------------------------------------------------------------
        | RIESGOS
        |--------------------------------------------------------------------------
        */
        $riesgos = User::create([
            'name' => 'Oscar Arroyo',
            'email' => 'riesgos@sesea.test',
            'password' => Hash::make('123456789'),
        ]);

        $riesgos->roles()->attach($capturaRole->id);

        $riesgos->coordinaciones()->attach([
            4
        ]);
    }
}
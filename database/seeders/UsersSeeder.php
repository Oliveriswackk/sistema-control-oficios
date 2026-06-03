<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Rol::where('clave', 'admin')->first();
        $capturaRole = Rol::where('clave', 'captura')->first();

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

        $permisoCerrar = \App\Models\Permiso::where(
            'clave',
            'puede_cerrar'
        )->first();

        $recepcion->permisos()->attach(
            $permisoCerrar->id
        );

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
    }
}
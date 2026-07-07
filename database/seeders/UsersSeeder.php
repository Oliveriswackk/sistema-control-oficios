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
        $admin = Rol::where('clave', 'admin')->firstOrFail();
        $colaborador = Rol::where('clave', 'colaborador')->firstOrFail();
        // $consulta = Rol::where('clave', 'consulta')->firstOrFail();

        $permisos = Permiso::all()->keyBy('clave');

        // ---------- Función auxiliar ----------
        $crear = function (
            string $nombre,
            string $email,
            string $password,
            Rol $rol,
            array $adscripciones,
            array $permisosExtra = []
        ) use ($permisos) {

            $user = User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $user->roles()->attach($rol->id);

            $user->coordinaciones()->attach($adscripciones);

            if (!empty($permisosExtra)) {

                $ids = collect($permisosExtra)
                    ->map(fn ($clave) => $permisos[$clave]->id)
                    ->toArray();

                $user->permisos()->attach($ids);
            }

            return $user;
        };


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADORES
        |--------------------------------------------------------------------------
        */

        $crear(
            'Oliver Coronado',
            'admin@sesea.test',
            'Oliwey777',
            $admin,
            [7]
        );

        $crear(
            'Salvador Jurado',
            'salvador.jurado@sesea.test',
            '123456789',
            $admin,
            [7]
        );

        $crear(
            'Noel Cuevas',
            'noel.cuevas@sesea.test',
            '123456789',
            $admin,
            [7]
        );

        /*
        |--------------------------------------------------------------------------
        | RECEPCION Y OFICIALIDAD DE PARTES
        |--------------------------------------------------------------------------
        */

        $crear(
            'Alejandro Salasplata',
            'felipe@sesea.test',
            '123456789',
            $colaborador,
            [6],
            [
                'puede_registrar_oficios',
                'puede_turnar',
                'puede_cerrar',
            ]
        );

        $crear(
            'Lizette Cordero',
            'liz@sesea.test',
            '123456789',
            $colaborador,
            [6],
            [
                'puede_registrar_oficios',
                'puede_turnar',
                'puede_cerrar',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | RECEPCIÓN
        |--------------------------------------------------------------------------
        */

        $crear(
            'Daniela Ruiz',
            'recepcion@sesea.test',
            '123456789',
            $colaborador,
            [6],
            [
                'puede_registrar_oficios',
                'puede_turnar',
                'puede_cerrar',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | COORDINADORES
        |--------------------------------------------------------------------------
        */

        $crear(
            'Héctor Ponce',
            'coord.riesgos@sesea.test',
            '123456789',
            $colaborador,
            [5],
            [
                'puede_returnar',
            ]
        );

        $crear(
            'Juan Carlos Estrada',
            'coord.vinculacion@sesea.test',
            '123456789',
            $colaborador,
            [10],
            [
                'puede_returnar',
            ]
        );

        $crear(
            'Leticia Favila',
            'coord.admin@sesea.test',
            '123456789',
            $colaborador,
            [4],
            [
                'puede_returnar',
            ]
        );

        $crear(
            'Dania Pérez',
            'coord.juridico@sesea.test',
            '123456789',
            $colaborador,
            [
                1,
                8,
            ],
            [
                'puede_returnar',
                'puede_ver_sensibles',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | COLABORADORES
        |--------------------------------------------------------------------------
        */

        $crear(
            'Óscar Arroyo',
            'oscar@sesea.test',
            '123456789',
            $colaborador,
            [5]
        );

        $crear(
            'Ximena García',
            'ximena@sesea.test',
            '123456789',
            $colaborador,
            [5]
        );

        $crear(
            'Beatriz Medrano',
            'beatriz@sesea.test',
            '123456789',
            $colaborador,
            [10]
        );

        $crear(
            'Anel Navarro',
            'anel@sesea.test',
            '123456789',
            $colaborador,
            [10]
        );

        $crear(
            'Roberto García',
            'roberto@sesea.test',
            '123456789',
            $colaborador,
            [4]
        );
    }
}
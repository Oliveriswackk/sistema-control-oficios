<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // PROTECCIÓN DE PRODUCCIÓN: Nunca ejecutar en producción por comando masivo
        if (App::environment('production')) {
            $this->command->warn('[SEGURIDAD] UsersSeeder fue omitido. No se pueden sembrar usuarios de prueba en Producción :( ');
            return;
        }

        $admin = Rol::where('clave', 'admin')->firstOrFail();
        $coordinador = Rol::where('clave', 'coordinador')->firstOrFail();
        $colaborador = Rol::where('clave', 'colaborador')->firstOrFail();
        $consulta = Rol::where('clave', 'consulta')->firstOrFail();

        $permisos = Permiso::all()->keyBy('clave');

        // ---------- Contraseña base por defecto ----------
        $defaultPassword = env('SEEDER_DEFAULT_PASSWORD', 'SESEA_OFICIOS2026');

        // ---------- Función auxiliar ----------
        $crear = function (
            string $nombre,
            string $email,
            ?string $password,
            array $roles,
            array $adscripciones,
            array $permisosExtra = []
        ) use ($permisos, $defaultPassword) {

            // Evitar duplicar si el usuario ya existe por email
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'password' => Hash::make($password ?? $defaultPassword),
                ]
            );

            // Sincronizar relaciones sin duplicar registros pivot
            $user->roles()->sync(collect($roles)->pluck('id')->toArray());
            $user->coordinaciones()->sync($adscripciones);

            if (!empty($permisosExtra)) {
                $ids = collect($permisosExtra)
                    ->map(fn ($clave) => $permisos[$clave]->id)
                    ->toArray();

                $user->permisos()->sync($ids);
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
            [$admin],
            [7]
        );

        $crear(
            'Salvador Jurado',
            'salvador.jurado@sesea.test',
            null,
            [$admin, $coordinador],
            [7]
        );

        $crear(
            'Noel Cuevas',
            'noel.cuevas@sesea.test',
            null,
            [$admin],
            [7]
        );

        /*
        |--------------------------------------------------------------------------
        | OFICIALIDAD DE PARTES
        |--------------------------------------------------------------------------
        */

        $crear(
            'Alejandro Salasplata',
            'felipe@sesea.test',
            null,
            [$colaborador],
            [6],
            ['puede_registrar_oficios', 'puede_turnar']
        );

        $crear(
            'Lizette Cordero',
            'liz@sesea.test',
            null,
            [$colaborador],
            [6],
            ['puede_registrar_oficios', 'puede_turnar']
        );

        /*
        |--------------------------------------------------------------------------
        | RECEPCIÓN
        |--------------------------------------------------------------------------
        */

        $crear(
            'Daniela Ruiz',
            'recepcion@sesea.test',
            null,
            [$colaborador],
            [6],
            ['puede_registrar_oficios', 'puede_turnar']
        );

        /*
        |--------------------------------------------------------------------------
        | COORDINADORES
        |--------------------------------------------------------------------------
        */

        $crear(
            'Héctor Ponce',
            'coord.riesgos@sesea.test',
            null,
            [$coordinador],
            [5],
            ['puede_returnar']
        );

        $crear(
            'Juan Carlos Estrada',
            'coord.vinculacion@sesea.test',
            null,
            [$coordinador],
            [10],
            ['puede_returnar']
        );

        $crear(
            'Leticia Favila',
            'coord.admin@sesea.test',
            null,
            [$coordinador],
            [4],
            ['puede_returnar']
        );

        $crear(
            'Dania Pérez',
            'coord.juridico@sesea.test',
            null,
            [$coordinador],
            [1, 8],
            ['puede_returnar', 'puede_ver_sensibles']
        );

        /*
        |--------------------------------------------------------------------------
        | COLABORADORES
        |--------------------------------------------------------------------------
        */

        $crear('Óscar Arroyo', 'oscar@sesea.test', null, [$colaborador], [5]);
        $crear('Ximena García', 'ximena@sesea.test', null, [$colaborador], [5]);
        $crear('Beatriz Medrano', 'beatriz@sesea.test', null, [$colaborador], [10]);
        $crear('Anel Navarro', 'anel@sesea.test', null, [$colaborador], [10]);
        $crear('Roberto García', 'roberto@sesea.test', null, [$colaborador], [4]);
    }
}
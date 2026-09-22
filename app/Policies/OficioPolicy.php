<?php

namespace App\Policies;

use App\Models\Oficio;
use App\Models\User;

class OficioPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin') && $ability !== 'cancelar') {
            return true;
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | VER OFICIO
    |--------------------------------------------------------------------------
    */
    public function view(User $user, Oficio $oficio): bool
    {
        if ($oficio->es_sensible &&
            !$user->hasPermission('puede_ver_sensibles')) {
            return false;
        }

        return
            $oficio->usuario_registro_id === $user->id
            || $user->coordinaciones()
                ->where('coordinaciones.id', $oficio->coordinacion_origen_id)
                ->exists()
            || $oficio->turnados()
                ->whereIn(
                    'coordinacion_id',
                    $user->coordinaciones->pluck('id')
                )
                ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function create(User $user): bool
    {
        return $user->hasPermission('puede_registrar_oficios');
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function update(User $user, Oficio $oficio): bool
    {
        if (
            in_array($oficio->estado->clave, [
                'cerrado',
                'cancelado',
            ])
        ) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('coordinador')) {
            return $oficio->turnados()
                ->whereIn(
                    'coordinacion_id',
                    $user->coordinaciones->pluck('id')
                )
                ->exists();
        }

        if ($oficio->usuario_registro_id === $user->id) {
            return true;
        }

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | TURNAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function turnar(User $user, Oficio $oficio): bool
    {
        if (!$user->hasPermission('puede_turnar')) {
            return false;
        }

        // Solo se puede turnar si está registrado o en seguimiento
        return in_array($oficio->estado->clave, [
            'registrado',
            'en_seguimiento'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | RETURNAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function returnar(User $user, Oficio $oficio): bool
    {
        if (!$user->hasPermission('puede_returnar')) {
            return false;
        }

        // Debe estar asignado al usuario
        return $oficio->turnados()
            ->whereIn(
                'coordinacion_id',
                $user->coordinaciones->pluck('id')
            )
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function cerrar(User $user, Oficio $oficio): bool
    {
        if (
            !$user->hasRole('admin') &&
            !$user->hasRole('coordinador')
        ) {
            return false;
        }

        return in_array($oficio->estado->clave, [
            'en_seguimiento',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CANCELAR
    |--------------------------------------------------------------------------
    */
    public function cancelar(User $user, Oficio $oficio): bool
    {
        if (
            $user->hasRole('admin') ||
            $user->hasRole('recepcion')
        ) {
            return !in_array($oficio->estado->clave, [
                'cerrado',
                'cancelado'
            ]);
        }

        return false;
    }

    
    /*
    |--------------------------------------------------------------------------
    | VER SENSIBLE
    |--------------------------------------------------------------------------
    */
    public function verSensible(User $user, Oficio $oficio): bool
    {
        if (!$oficio->es_sensible) {
            return true;
        }

        return $user->hasPermission('puede_ver_sensibles');
    }

    /*
    |--------------------------------------------------------------------------
    | SUBIR ARCHIVO
    |--------------------------------------------------------------------------
    */
    public function subirArchivo(User $user, Oficio $oficio): bool
    {
        if (
            in_array($oficio->estado->clave, [
                'cerrado',
                'cancelado',
            ])
        ) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return
            $user->hasPermission('puede_registrar_oficios') &&
            $oficio->usuario_registro_id === $user->id;
    }
}
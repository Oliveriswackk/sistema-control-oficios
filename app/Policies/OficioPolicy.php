<?php

namespace App\Policies;

use App\Models\Oficio;
use App\Models\User;

class OficioPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VER OFICIO
    |--------------------------------------------------------------------------
    */
    public function view(User $user, Oficio $oficio): bool
    {
        // Admin ve todo
        if ($user->hasRole('admin')) {
            return true;
        }

        // Puede ver sensibles solo con permiso
        if ($oficio->es_sensible && !$user->hasPermission('puede_ver_sensibles')) {
            return false;
        }

        // Puede ver si pertenece a su coordinación o fue turnado a él
        return $oficio->usuario_registro_id === $user->id
            || $oficio->turnados()->where('usuario_id', $user->id)->exists();
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
        if ($user->hasRole('admin')) {
            return true;
        }

        // Solo creador puede editar si aún no está turnado
        if ($oficio->estado->clave === 'registrado') {
            return $oficio->usuario_registro_id === $user->id;
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
            ->where('usuario_id', $user->id)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | CERRAR OFICIO
    |--------------------------------------------------------------------------
    */
    public function cerrar(User $user, Oficio $oficio): bool
    {
        if (!$user->hasPermission('puede_cerrar')) {
            return false;
        }

        return in_array($oficio->estado->clave, [
            'en_seguimiento',
            'turnado'
        ]);
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

        return $user->hasPermission('puede_ver_sensibles')
            || $user->hasRole('admin');
    }
}
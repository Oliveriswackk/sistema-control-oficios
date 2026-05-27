<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Rol;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Oliver',
            'email' => 'asesor.sesea.chihuahua@gmail.com',
            'password' => Hash::make('Oliwey777'),
        ]);

        $adminRole = Rol::where('clave', 'admin')->first();

        $user->roles()->attach($adminRole->id);
    }
}
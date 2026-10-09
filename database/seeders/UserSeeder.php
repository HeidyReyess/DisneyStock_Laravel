<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de roles
        $adminId    = DB::table('roles')->where('nombre', 'administrador')->value('id');
        $empleadoId = DB::table('roles')->where('nombre', 'empleado')->value('id');

        DB::table('users')->insert([
            [
                'rol_id'       => $adminId,
                'nombre'       => 'Administrador',
                'usuario'      => 'admin',
                'email'        => 'admin@disneystock.com',
                'password'     => Hash::make('admin123'),
                'estado'       => 'activo',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'rol_id'       => $empleadoId,
                'nombre'       => 'Empleado Demo',
                'usuario'      => 'empleado',
                'email'        => 'empleado@disneystock.com',
                'password'     => Hash::make('empleado123'),
                'estado'       => 'activo',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}

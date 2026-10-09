<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'administrador', 'descripcion' => 'Acceso total al sistema', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'empleado',      'descripcion' => 'Acceso a ventas e inventario', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,      // 1. Primero los roles
            UserSeeder::class,     // 2. Luego admin + empleado (necesita rol_id)
            CategoriaSeeder::class, // 3. Categorías iniciales de Variedades Disney
        ]);
    }
}

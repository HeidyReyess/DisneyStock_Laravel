<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre_categoria' => 'Manillas',   'descripcion' => 'Pulseras y manillas de todo tipo'],
            ['nombre_categoria' => 'Vestidos',   'descripcion' => 'Vestidos y faldas para dama'],
            ['nombre_categoria' => 'Accesorios', 'descripcion' => 'Collares, aretes y accesorios varios'],
            ['nombre_categoria' => 'Ropa',       'descripcion' => 'Prendas de vestir en general'],
            ['nombre_categoria' => 'Bolsos',     'descripcion' => 'Bolsos, carteras y monederos'],
            ['nombre_categoria' => 'Zapatos',    'descripcion' => 'Calzado para dama y niña'],
            ['nombre_categoria' => 'Ropa Niña',  'descripcion' => 'Prendas de vestir para niñas'],
            ['nombre_categoria' => 'Bisutería',  'descripcion' => 'Joyas y adornos de bisutería'],
            ['nombre_categoria' => 'Cinturones', 'descripcion' => 'Cinturones y correas de moda'],
            ['nombre_categoria' => 'Gorras',     'descripcion' => 'Gorras, sombreros y viseras'],
        ];

        foreach ($categorias as $cat) {
            DB::table('categorias')->insert(array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

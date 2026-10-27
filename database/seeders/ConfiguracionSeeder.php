<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [
            [
                'clave'       => 'nombre_negocio',
                'valor'       => 'Variedades Disney',
                'descripcion' => 'Nombre del negocio que aparece en comprobantes y encabezados.',
            ],
            [
                'clave'       => 'nit_negocio',
                'valor'       => '',
                'descripcion' => 'NIT o identificación tributaria del negocio.',
            ],
            [
                'clave'       => 'direccion_negocio',
                'valor'       => '',
                'descripcion' => 'Dirección física del negocio.',
            ],
            [
                'clave'       => 'telefono_negocio',
                'valor'       => '',
                'descripcion' => 'Teléfono de contacto del negocio.',
            ],
            [
                'clave'       => 'prefijo_factura',
                'valor'       => 'DS-',
                'descripcion' => 'Prefijo que se antepone al número de factura (ej. DS-000001).',
            ],
            [
                'clave'       => 'porcentaje_iva',
                'valor'       => '0',
                'descripcion' => 'Porcentaje de IVA aplicado sobre (subtotal - descuento). Use 0 para no aplicar IVA.',
            ],
            [
                'clave'       => 'descuento_maximo_porcentaje',
                'valor'       => '100',
                'descripcion' => 'Descuento máximo permitido por venta, expresado como porcentaje del subtotal (0–100).',
            ],
            [
                'clave'       => 'stock_minimo_defecto',
                'valor'       => '1',
                'descripcion' => 'Valor por defecto del stock mínimo al crear un nuevo producto.',
            ],
        ];

        foreach ($configuraciones as $config) {
            DB::table('configuraciones')->insertOrIgnore(array_merge($config, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

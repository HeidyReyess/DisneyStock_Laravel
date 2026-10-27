<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfiguracionController extends Controller
{
    // GET /configuracion
    public function index()
    {
        // Carga todas las claves en un array asociativo para la vista
        $config = Configuracion::all()->pluck('valor', 'clave');

        return view('admin.configuracion.index', compact('config'));
    }

    // POST /configuracion
    public function update(Request $request)
    {
        $request->validate([
            'nombre_negocio'               => ['required', 'string', 'max:150'],
            'nit_negocio'                  => ['nullable', 'string', 'max:50'],
            'direccion_negocio'            => ['nullable', 'string', 'max:200'],
            'telefono_negocio'             => ['nullable', 'string', 'max:50'],
            'prefijo_factura'              => ['required', 'string', 'max:10'],
            'porcentaje_iva'               => ['required', 'numeric', 'min:0', 'max:100'],
            'descuento_maximo_porcentaje'  => ['required', 'numeric', 'min:0', 'max:100'],
            'stock_minimo_defecto'         => ['required', 'integer', 'min:1'],
        ], [
            'nombre_negocio.required'              => 'El nombre del negocio es obligatorio.',
            'nombre_negocio.max'                   => 'El nombre no puede superar 150 caracteres.',
            'prefijo_factura.required'             => 'El prefijo de factura es obligatorio.',
            'prefijo_factura.max'                  => 'El prefijo no puede superar 10 caracteres.',
            'porcentaje_iva.required'              => 'El porcentaje de IVA es obligatorio.',
            'porcentaje_iva.numeric'               => 'El IVA debe ser un número.',
            'porcentaje_iva.min'                   => 'El IVA no puede ser negativo.',
            'porcentaje_iva.max'                   => 'El IVA no puede superar el 100%.',
            'descuento_maximo_porcentaje.required' => 'El descuento máximo es obligatorio.',
            'descuento_maximo_porcentaje.numeric'  => 'El descuento máximo debe ser un número.',
            'descuento_maximo_porcentaje.min'      => 'El descuento máximo no puede ser negativo.',
            'descuento_maximo_porcentaje.max'      => 'El descuento máximo no puede superar el 100%.',
            'stock_minimo_defecto.required'        => 'El stock mínimo por defecto es obligatorio.',
            'stock_minimo_defecto.integer'         => 'El stock mínimo debe ser un número entero.',
            'stock_minimo_defecto.min'             => 'El stock mínimo debe ser al menos 1.',
        ]);

        $claves = [
            'nombre_negocio',
            'nit_negocio',
            'direccion_negocio',
            'telefono_negocio',
            'prefijo_factura',
            'porcentaje_iva',
            'descuento_maximo_porcentaje',
            'stock_minimo_defecto',
        ];

        foreach ($claves as $clave) {
            Configuracion::establecer($clave, $request->input($clave, ''));
        }

        return redirect()->route('configuracion.index')
            ->with('success', 'Configuración guardada correctamente.');
    }
}

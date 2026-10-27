<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table    = 'configuraciones';
    protected $fillable = ['clave', 'valor', 'descripcion'];

    /**
     * Obtiene el valor de una clave de configuración.
     * Usa caché de 60 minutos para evitar consultas repetidas.
     */
    public static function valor(string $clave, mixed $defecto = null): mixed
    {
        $resultado = Cache::remember("config_{$clave}", 3600, function () use ($clave) {
            return static::where('clave', $clave)->value('valor');
        });

        return $resultado ?? $defecto;
    }

    /**
     * Guarda un valor y limpia su caché.
     */
    public static function establecer(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget("config_{$clave}");
    }
}

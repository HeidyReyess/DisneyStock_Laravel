<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alerta extends Model
{
    use HasFactory;

    protected $table    = 'alertas';
    protected $fillable = [
        'tipo_alerta', 'mensaje', 'fecha_alerta',
        'fecha_resolucion', 'estado', 'producto_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_alerta'      => 'date',
            'fecha_resolucion'  => 'date',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $table    = 'ventas';
    protected $fillable = [
        'fecha_venta', 'subtotal', 'impuesto',
        'descuento', 'total', 'estado', 'user_id',
    ];

    protected function casts(): array
    {
        return ['fecha_venta' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'venta_id');
    }

    public function estaAnulada(): bool
    {
        return $this->estado === 'anulada';
    }
}

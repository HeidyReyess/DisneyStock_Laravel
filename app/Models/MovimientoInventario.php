<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table    = 'movimiento_inventarios';
    protected $fillable = [
        'tipo_movimiento', 'cantidad', 'fecha',
        'descripcion', 'proveedor', 'producto_id', 'user_id', 'venta_id',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

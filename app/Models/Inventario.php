<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;

    protected $table    = 'inventarios';
    protected $fillable = ['cantidad_stock', 'stock_minimo', 'fecha_actualizacion', 'producto_id'];

    protected function casts(): array
    {
        return ['fecha_actualizacion' => 'date'];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}

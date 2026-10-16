<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table    = 'productos';
    protected $fillable = [
        'nombre', 'precio_venta', 'precio_compra',
        'fecha_ingreso', 'estado', 'proveedor', 'imagen', 'categoria_id',
    ];

    protected function casts(): array
    {
        return ['fecha_ingreso' => 'date'];
    }

    // ── Relaciones ────────────────────────────────────────
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function inventario()
    {
        return $this->hasOne(Inventario::class, 'producto_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    // ── Helpers ───────────────────────────────────────────
    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function stockActual(): int
    {
        return $this->inventario?->cantidad_stock ?? 0;
    }

    public function stockMinimo(): int
    {
        return $this->inventario?->stock_minimo ?? 0;
    }

    public function stockBajo(): bool
    {
        $inv = $this->inventario;
        if (!$inv || $inv->stock_minimo <= 0) return false;
        return $inv->cantidad_stock <= $inv->stock_minimo;
    }

    // Bloquea eliminación si tiene ventas o movimientos
    public function tieneDependencias(): bool
    {
        return $this->detalleVentas()->exists() || $this->movimientos()->exists();
    }
}

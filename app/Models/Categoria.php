<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;

    protected $table    = 'categorias';
    protected $fillable = ['nombre_categoria', 'descripcion'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Verifica si tiene productos asociados — bloquea eliminación
    public function tieneProductos(): bool
    {
        return $this->productos()->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $table    = 'facturas';
    protected $fillable = ['numero', 'fecha_emision', 'total', 'formato', 'venta_id'];

    protected function casts(): array
    {
        return ['fecha_emision' => 'date'];
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}

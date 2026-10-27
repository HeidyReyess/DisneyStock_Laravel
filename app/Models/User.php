<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'rol_id',
        'nombre',
        'usuario',
        'email',
        'password',
        'estado',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function ventas()
    {
        return $this->hasMany(\App\Models\Venta::class, 'user_id');
    }

    public function movimientos()
    {
        return $this->hasMany(\App\Models\MovimientoInventario::class, 'user_id');
    }

    // Helpers de conveniencia
    public function esAdmin(): bool
    {
        return ($this->rol->nombre ?? '') === 'administrador';
    }

    public function esEmpleado(): bool
    {
        return ($this->rol->nombre ?? '') === 'empleado';
    }
}

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

    // Laravel usa este método para saber con qué columna autenticar
    public function getAuthIdentifierName(): string
    {
        return 'usuario';
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

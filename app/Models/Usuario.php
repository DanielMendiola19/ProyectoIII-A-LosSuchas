<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // <- Cambiado
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['nombre', 'apellido', 'correo', 'contrasena', 'rol_id'];

    // Cambiar el nombre del campo de password para Auth
    protected $hidden = ['contrasena', 'remember_token'];

    public function getAuthPassword() {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
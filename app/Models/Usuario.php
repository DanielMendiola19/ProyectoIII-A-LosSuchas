<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['nombre', 'apellido', 'correo', 'contrasena', 'rol_id'];

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

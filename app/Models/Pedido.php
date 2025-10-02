<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['fecha_pedido', 'estado', 'metodo_pago', 'total', 'usuario_id', 'mesa_id'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}

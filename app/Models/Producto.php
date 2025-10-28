<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'categoria_id',
        'imagen'
    ];

    // Relación con categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class)->withTrashed();
    }

    public function reportes()
    {
        return $this->hasMany(ReporteDiario::class);
    }

    // 🔹 NUEVO: Scope para forzar incluir eliminados en ciertos casos
    public function scopeWithTrashedIfNeeded($query, $includeTrashed = true)
    {
        return $includeTrashed ? $query->withTrashed() : $query;
    }
}

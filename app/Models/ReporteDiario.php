<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteDiario extends Model
{
    protected $table = 'reporte_diario';
    protected $fillable = ['fecha', 'producto_id', 'vendidos', 'stock_final'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reception_id', 'product_id', 'tipo_producto', 'lote', 
        'fecha_vencimiento', 'cantidad', 'valor_unitario', 'valor_total_item'
    ];

    public function reception()
    {
        return $this->belongsTo(Reception::class, 'reception_id');
    }

    // Aquí enlazamos con tu modelo de tabla inventario/productos
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id'); 
    }
}
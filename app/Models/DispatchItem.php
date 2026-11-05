<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    protected $fillable = [
        'dispatch_id', 'inventory_id', 'product_id', 'producto', 'lote', 'fecha_vencimiento', 'programa', 'cantidad'
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
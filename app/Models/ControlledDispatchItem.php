<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlledDispatchItem extends Model
{
    protected $fillable = [
        'controlled_dispatch_id', 'inventory_id', 'product_id', 'producto', 'lote', 'fecha_vencimiento', 'programa', 'cantidad'
    ];

    public function dispatch()
    {
        return $this->belongsTo(ControlledDispatch::class, 'controlled_dispatch_id');
    }

    public function controlledDispatch()
    {
        return $this->belongsTo(ControlledDispatch::class, 'controlled_dispatch_id');
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
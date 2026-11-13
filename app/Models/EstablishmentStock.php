<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// saldo de un producto en un establecimiento de la red
class EstablishmentStock extends Model
{
    protected $fillable = ['establecimiento', 'product_id', 'producto', 'cantidad'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movimientos()
    {
        return $this->hasMany(EstablishmentStockMovement::class);
    }
}

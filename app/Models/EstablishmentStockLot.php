<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// lote de un producto que tiene un establecimiento de la red
class EstablishmentStockLot extends Model
{
    protected $fillable = ['establishment_stock_id', 'lote', 'fecha_vencimiento', 'cantidad'];

    protected $casts = ['fecha_vencimiento' => 'date'];

    public function stock()
    {
        return $this->belongsTo(EstablishmentStock::class, 'establishment_stock_id');
    }
}

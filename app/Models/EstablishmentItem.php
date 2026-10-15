<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstablishmentItem extends Model
{
    protected $fillable = [
        'establishment_request_id',
        'product_id',
        'consumo_anterior',
        'stock_actual',
        'cantidad_solicitada',
        'cantidad_despachada',
    ];

    public function request()
    {
        return $this->belongsTo(EstablishmentRequest::class, 'establishment_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

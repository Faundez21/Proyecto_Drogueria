<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// cada cambio del saldo de un establecimiento
class EstablishmentStockMovement extends Model
{
    protected $fillable = [
        'establishment_stock_id', 'user_id', 'tipo', 'cantidad', 'saldo',
        'referencia', 'dispatch_id', 'establishment_request_id',
    ];

    public function stock()
    {
        return $this->belongsTo(EstablishmentStock::class, 'establishment_stock_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

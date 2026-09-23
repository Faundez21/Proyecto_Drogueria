<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'correlativo', 'fecha_despacho', 'estado_liberacion', 
        'establecimiento_destino', 'direccion_destino', 'condiciones_transporte'
    ];

    public function items()
    {
        return $this->hasMany(DispatchItem::class);
    }
}
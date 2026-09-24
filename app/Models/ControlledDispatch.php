<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlledDispatch extends Model
{
    protected $fillable = [
        'correlativo', 'fecha_despacho', 'clasificacion', 'estado_liberacion', 
        'establecimiento_destino', 'direccion_destino', 'condiciones_transporte',
        'series_recetas', 'firma_despacha', 'firma_recibe'
    ];

    public function items()
    {
        return $this->hasMany(ControlledDispatchItem::class);
    }
}
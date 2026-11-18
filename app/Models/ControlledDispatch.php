<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// una guia de despacho de estupefacientes y psicotropicos
class ControlledDispatch extends Model
{
    protected $fillable = [
        'establishment_request_id', 'user_id',
        'correlativo', 'fecha_despacho', 'clasificacion', 'estado_liberacion', 
        'establecimiento_destino', 'direccion_destino', 'condiciones_transporte',
        'series_recetas', 'firma_despacha', 'firma_recibe'
    ];

    public function items()
    {
        return $this->hasMany(ControlledDispatchItem::class);
    }

    public function establishmentRequest()
    {
        return $this->belongsTo(EstablishmentRequest::class, 'establishment_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
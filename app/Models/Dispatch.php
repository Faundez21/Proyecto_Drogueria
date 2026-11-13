<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// una guia de despacho de productos normales (no controlados)
class Dispatch extends Model
{
    protected $fillable = [
        'establishment_request_id', 'user_id',
        'correlativo', 'fecha_despacho', 'clasificacion', 'establecimiento_origen', 'estado_liberacion', 
        'establecimiento_destino', 'direccion_destino', 'condiciones_transporte',
        'firma_despacha', 'firma_recibe',
        'motivo_traspaso', 'estado_recepcion', 'fecha_recepcion',
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
    ];

    // traspaso interno: despacho a la red sin solicitud previa
    public function esTraspasoInterno(): bool
    {
        return ! empty($this->motivo_traspaso);
    }

    // la guia se emitio sin la firma de quien recibe y aun no se registra la recepcion
    public function pendienteRecepcion(): bool
    {
        return $this->estado_recepcion === 'Pendiente';
    }

    public function items()
    {
        return $this->hasMany(DispatchItem::class);
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Una guía de despacho de productos normales (no controlados).
 *
 * El correlativo es anual y con formato GD-AAAA-XXXX. Si el despacho salió de
 * una solicitud, queda enlazado a ella por establishment_request_id.
 */
class Dispatch extends Model
{
    protected $fillable = [
        'establishment_request_id', 'user_id',
        'correlativo', 'fecha_despacho', 'clasificacion', 'estado_liberacion', 
        'establecimiento_destino', 'direccion_destino', 'condiciones_transporte',
        'firma_despacha', 'firma_recibe'
    ];

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
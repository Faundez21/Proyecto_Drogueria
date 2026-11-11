<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Una guía de despacho de estupefacientes y psicotrópicos.
 *
 * Va separada de Dispatch porque estos productos tienen control legal propio:
 * su correlativo es GDC-AAAA-XXXX, solo pueden despacharse a un CESFAM y
 * permiten registrar las series de las recetas cheque.
 */
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
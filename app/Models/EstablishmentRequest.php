<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstablishmentRequest extends Model
{
    protected $fillable = [
        'establecimiento',
        'fecha_solicitud',
        'numero_solicitud',
        'responsable',
        'cargo',
        'observaciones',
        'firma_solicitante',
        'estado',
    ];

    public function items()
    {
        return $this->hasMany(EstablishmentItem::class);
    }
}

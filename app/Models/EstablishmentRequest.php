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
        'assigned_user_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function items()
    {
        return $this->hasMany(EstablishmentItem::class);
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'establishment_request_id');
    }

    public function controlledDispatches()
    {
        return $this->hasMany(ControlledDispatch::class, 'establishment_request_id');
    }
}

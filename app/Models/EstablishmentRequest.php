<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * El pedido que hace un establecimiento a la droguería.
 *
 * El campo estado lo recalcula SolicitudService según lo que se haya
 * despachado: Pendiente, Asignada, Parcial o Despachado. Una misma solicitud
 * puede cubrirse con varias guías, e incluso repartirse entre el despacho
 * normal y el de controlados, por eso tiene las dos relaciones de despacho.
 */
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

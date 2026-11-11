<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un movimiento del Kardex: la historia de un lote.
 *
 * Se crea una fila cada vez que un lote se recibe, cambia de estado, se
 * reubica o se despacha, guardando quién lo hizo, cuándo, cuánto y con qué
 * documento de respaldo. De acá sale la vista de Trazabilidad.
 */
class StockMovement extends Model
{
    protected $fillable = [
        'inventory_id',
        'user_id',
        'producto',
        'lote',
        'fecha_vencimiento',
        'tipo_movimiento',
        'cantidad',
        'fecha_hora',
        'origen_destino',
        'documento_respaldo',
        'estado_lote',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_vencimiento' => 'date',
        'cantidad' => 'integer',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
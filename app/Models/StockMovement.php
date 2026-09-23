<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'producto', 'lote', 'fecha_vencimiento', 'tipo_movimiento',
        'cantidad', 'fecha_hora', 'origen_destino', 'documento_respaldo', 'estado_lote'
    ];
}
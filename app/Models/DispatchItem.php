<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    protected $fillable = [
        'dispatch_id', 'producto', 'lote', 'fecha_vencimiento', 'programa', 'cantidad'
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }
}
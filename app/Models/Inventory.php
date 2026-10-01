<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // <-- AGREGA ESTA LÍNEA AQUÍ
        'correlativo_interno',
        'fecha_recepcion',
        'tipo_documento',
        'numero_documento',
        'orden_compra',
        'origen_mercaderia',
        'rut_proveedor',
        'razon_social',
        'producto',
        'categoria',
        'estado',
        'motivo',
        'lote',
        'fecha_vencimiento',
        'cantidad',
        'valor_neto_unitario',
        'pasillo',
        'estante',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
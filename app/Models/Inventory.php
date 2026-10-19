<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
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
        'programa',
        'nivel',
        'posicion',
        'solicitud_pedido_mensual',
        'solicitud_stock_critico',
        'adelanto_de_pedido',
        'traspaso',
    ];

    protected $casts = [
        'solicitud_pedido_mensual' => 'boolean',
        'solicitud_stock_critico' => 'boolean',
        'adelanto_de_pedido' => 'boolean',
        'traspaso' => 'boolean',
        'cantidad' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function dispatchItems()
    {
        return $this->hasMany(DispatchItem::class);
    }
}
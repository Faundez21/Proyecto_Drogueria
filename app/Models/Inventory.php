<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'user_id',
=======
        'product_id',
        'user_id',
        'provider_id',
        'reception_id',
>>>>>>> origin/cristobal
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
        'minimo',
        'valor_neto_unitario',
        'pasillo',
        'estante',
        'aisle_id',
        'shelf_id',
        'programa',
        'nivel',
        'level_id',
        'posicion',
        'position_id',
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
        'minimo' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
<<<<<<< HEAD
}
=======

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function aisle()
    {
        return $this->belongsTo(Aisle::class);
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function dispatchItems()
    {
        return $this->hasMany(DispatchItem::class);
    }

    public function controlledDispatchItems()
    {
        return $this->hasMany(ControlledDispatchItem::class);
    }
}
>>>>>>> origin/cristobal

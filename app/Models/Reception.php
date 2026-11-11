<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;

    protected $fillable = [
        'correlativo', 'fecha_recepcion', 'tipo_documento', 'numero_documento', 
        'orden_compra', 'origen', 'provider_id', 'valor_total_recepcion', 
        'firma_proveedor', 'firma_receptor'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function items()
    {
        return $this->hasMany(ReceptionItem::class, 'reception_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'reception_id');
    }
}
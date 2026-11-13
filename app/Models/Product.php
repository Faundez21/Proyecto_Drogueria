<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // permitimos que el seeder pueda llenar el nombre
    protected $fillable = [
        'nombre',
    ];

    public function receptionItems()
    {
        return $this->hasMany(ReceptionItem::class, 'product_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_id');
    }

    public function establishmentItems()
    {
        return $this->hasMany(EstablishmentItem::class, 'product_id');
    }

    public function dispatchItems()
    {
        return $this->hasMany(DispatchItem::class, 'product_id');
    }

    public function controlledDispatchItems()
    {
        return $this->hasMany(ControlledDispatchItem::class, 'product_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// un pasillo de la bodega
class Aisle extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color'];

    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'aisle_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Un pasillo de la bodega.
 *
 * Es el primer nivel de la estructura física: la bodega tiene pasillos, cada
 * pasillo tiene estanterías, cada estantería tiene niveles y cada nivel tiene
 * posiciones. Un lote sin ubicación asignada queda marcado como "P.A".
 */
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

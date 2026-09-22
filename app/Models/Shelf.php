<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Level;
class Shelf extends Model
{
    protected $fillable = [
        'name',
        'description',
        'family_category',
        'aisle_id',
        'pos_x',
        'pos_z',
        'rotation'
    ];

    // relacion una estantería pertenece a un pasillo
    public function aisle()
    {
        return $this->belongsTo(Aisle::class);
    }
    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}

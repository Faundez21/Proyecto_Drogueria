<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shelf;

class Level extends Model
{
    protected $fillable = [
        'number',
        'description',
        'shelf_id',
    ];

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'level_id');
    }
}

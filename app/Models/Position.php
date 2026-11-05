<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'name',
        'level_id',
        'description',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'position_id');
    }
}

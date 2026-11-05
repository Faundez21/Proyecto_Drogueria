<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut', 
        'name', 
        'business_line', 
        'category', 
        'email', 
        'phone', 
        'address', 
        'status'
    ];

    public function receptions()
    {
        return $this->hasMany(Reception::class, 'provider_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'provider_id');
    }
}
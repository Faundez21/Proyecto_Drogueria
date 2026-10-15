<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Permitimos que el seeder pueda llenar el nombre
    protected $fillable = [
        'nombre',
    ];

    // Relación con ReceptionItems (opcional pero recomendada para el futuro)
    public function receptionItems()
    {
        return $this->hasMany(ReceptionItem::class, 'product_id');
    }

}

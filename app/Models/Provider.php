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
        'bank_name', 
        'account_type', 
        'account_number', 
        'status'
    ];
}
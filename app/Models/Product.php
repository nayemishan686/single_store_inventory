<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'quantity', 'description'];
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}

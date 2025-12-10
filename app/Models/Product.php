<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'category_id',
    'supplier_id',
    'name',
    'barcode',
    'stock',
    'min_stock',
    'unit',
    'buy_price',
    'sell_price'
];

// Kita siapkan relasinya sekalian
public function category()
{
    return $this->belongsTo(Category::class);
}

public function supplier()
{
    return $this->belongsTo(Supplier::class);
}
}

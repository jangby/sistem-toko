<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = ['purchase_id', 'product_id', 'request_qty', 'received_qty', 'buy_price'];

public function product() { return $this->belongsTo(Product::class); }
}

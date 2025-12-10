<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['po_number', 'supplier_id', 'date', 'status', 'total_estimated'];

public function supplier() { return $this->belongsTo(Supplier::class); }
public function details() { return $this->hasMany(PurchaseDetail::class); }
}

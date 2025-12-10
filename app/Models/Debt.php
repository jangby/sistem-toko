<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = ['type', 'name', 'phone', 'amount', 'paid_amount', 'due_date', 'description', 'status'];

public function payments()
{
    return $this->hasMany(DebtPayment::class)->latest();
}

// Helper untuk hitung sisa
public function getRemainingAttribute()
{
    return $this->amount - $this->paid_amount;
}
}

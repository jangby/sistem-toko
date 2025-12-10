<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    protected $fillable = ['debt_id', 'amount', 'date'];

public function debt()
{
    return $this->belongsTo(Debt::class);
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // KITA HARUS MENDAFTARKAN KOLOM INI AGAR BISA DI-INPUT
    protected $fillable = [
        'invoice_no',
        'user_id',
        'total_amount',
        'pay_amount',
        'change_amount',
        'payment_method',
    ];

    // Relasi: 1 Transaksi punya banyak Detail
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
    
    // Relasi: Transaksi dilayani oleh User (Kasir)
    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
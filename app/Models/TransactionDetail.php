<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'qty',
        'price',
    ];

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // --- TAMBAHKAN BAGIAN INI (YANG HILANG) ---
    // Relasi balik ke Transaksi Induk
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
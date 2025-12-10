<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Hari Ini
        $today = Carbon::today();
        
        // Hitung Omset Hari Ini
        $omsetToday = Transaction::whereDate('created_at', $today)->sum('total_amount');
        
        // Hitung Jumlah Transaksi Hari Ini
        $trxToday = Transaction::whereDate('created_at', $today)->count();
        
        // 2. Data Stok
        // Cari barang yang stoknya <= batas minimum (Kritis)
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        return view('dashboard', compact('omsetToday', 'trxToday', 'lowStockCount'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\CashMutation; // Tambahkan ini
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // 1. Data Hari Ini (Untuk KPI Kecil)
        $omsetToday = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $trxToday = Transaction::whereDate('created_at', $today)->count();
        
        // 2. Data Stok (Untuk KPI Kecil)
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        // 3. HITUNG SALDO KAS AKTUAL (REAL-TIME)
        // Rumus: (Total Semua Penjualan + Total Pemasukan Manual) - Total Pengeluaran Manual
        $allSales = Transaction::sum('total_amount');
        $manualIn = CashMutation::where('type', 'in')->sum('amount');
        $manualOut = CashMutation::where('type', 'out')->sum('amount'); // Termasuk transfer ke tabungan

        $currentCashBalance = ($allSales + $manualIn) - $manualOut;

        return view('dashboard', compact('omsetToday', 'trxToday', 'lowStockCount', 'currentCashBalance'));
    }
}
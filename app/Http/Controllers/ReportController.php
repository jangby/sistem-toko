<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 2. Query Dasar
        $transactions = Transaction::whereDate('created_at', '>=', $startDate)
                                   ->whereDate('created_at', '<=', $endDate);

        // 3. Statistik Utama
        $totalOmset = $transactions->sum('total_amount');
        $totalTransaksi = $transactions->count();
        
        // Menghitung Item Terjual (Harus join ke detail)
        $totalItemTerjual = TransactionDetail::whereHas('transaction', function($q) use ($startDate, $endDate) {
            $q->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate);
        })->sum('qty');

        // 4. Data Riwayat (Paginate biar ringan)
        // Kita clone query agar tidak merusak query sum di atas
        $history = (clone $transactions)->with('cashier')->latest()->paginate(20);

        // 5. Top Produk (Produk Terlaris)
        $topProducts = TransactionDetail::select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'startDate', 'endDate', 
            'totalOmset', 'totalTransaksi', 'totalItemTerjual',
            'history', 'topProducts'
        ));
    }
}
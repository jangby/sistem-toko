<?php

namespace App\Http\Controllers;

use App\Models\CashMutation;
use App\Models\Transaction; // Kita butuh ini untuk hitung omset penjualan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashController extends Controller
{
    public function index(Request $request)
    {
        // Filter Bulan (Default bulan ini)
        $month = $request->input('month', date('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        // 1. Hitung Saldo Awal (Semua uang sebelum bulan ini)
        $saldoAwalPenjualan = Transaction::where('created_at', '<', $start)->sum('total_amount');
        $saldoAwalManualIn = CashMutation::where('type', 'in')->where('date', '<', $start)->sum('amount');
        $saldoAwalManualOut = CashMutation::where('type', 'out')->where('date', '<', $start)->sum('amount');
        $saldoAwal = ($saldoAwalPenjualan + $saldoAwalManualIn) - $saldoAwalManualOut;

        // 2. Transaksi Bulan Ini
        $mutations = CashMutation::whereBetween('date', [$start, $end])
                        ->latest('date')
                        ->latest('id')
                        ->get();
        
        // 3. Penjualan Bulan Ini (Kita anggap sebagai Cash In Harian)
        // Opsional: Jika ingin menggabungkan list penjualan ke dalam list mutasi, logic-nya agak kompleks.
        // Untuk simpelnya, di halaman ini kita fokus ke Operasional saja, tapi SALDO UTAMA tetap menghitung penjualan.
        
        $totalPenjualanBulanIni = Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $totalMasukBulanIni = $mutations->where('type', 'in')->sum('amount');
        $totalKeluarBulanIni = $mutations->where('type', 'out')->sum('amount');

        // Saldo Akhir Realtime
        $saldoAkhir = $saldoAwal + $totalPenjualanBulanIni + $totalMasukBulanIni - $totalKeluarBulanIni;

        return view('cash.index', compact(
            'mutations', 'saldoAwal', 'saldoAkhir', 
            'totalPenjualanBulanIni', 'totalMasukBulanIni', 'totalKeluarBulanIni',
            'month'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        CashMutation::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->back()->with('success', 'Transaksi kas berhasil dicatat!');
    }

    public function destroy(CashMutation $cash)
    {
        $cash->delete();
        return redirect()->back()->with('success', 'Catatan dihapus!');
    }
}
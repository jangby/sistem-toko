<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\CashMutation; // Kita butuh akses ke Kas Toko
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SavingController extends Controller
{
    public function index()
    {
        // Hitung Saldo Tabungan
        $totalDeposit = Saving::where('type', 'deposit')->sum('amount');
        $totalWithdraw = Saving::where('type', 'withdrawal')->sum('amount');
        $balance = $totalDeposit - $totalWithdraw;

        $history = Saving::latest()->paginate(10);

        return view('savings.index', compact('balance', 'history'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:1',
            'source' => 'required|in:manual,cash',
            'date' => 'required|date',
        ]);

        // Cek Saldo dulu jika mau narik
        if ($request->type == 'withdrawal') {
            $currentBalance = Saving::where('type', 'deposit')->sum('amount') - Saving::where('type', 'withdrawal')->sum('amount');
            if ($request->amount > $currentBalance) {
                return back()->with('error', 'Saldo tabungan tidak cukup!');
            }
        }

        DB::transaction(function () use ($request) {
            // 1. Simpan ke Tabel Savings
            Saving::create([
                'date' => $request->date,
                'type' => $request->type,
                'amount' => $request->amount,
                'source' => $request->source,
                'description' => $request->description,
            ]);

            // 2. LOGIKA POTONG KAS (Hanya jika Nabung & Sumber dari Kas)
            if ($request->type == 'deposit' && $request->source == 'cash') {
                CashMutation::create([
                    'user_id' => Auth::id(),
                    'type' => 'out', // Pengeluaran Kas Toko
                    'amount' => $request->amount,
                    'description' => 'Disisihkan ke Tabungan/Dana Darurat',
                    'date' => $request->date,
                ]);
            }
            
            // Opsional: Jika Narik Tabungan dan mau dimasukkan kembali ke Kas Toko
            // Bisa tambahkan logika di sini jika diperlukan.
            // Saat ini asumsinya Narik Tabungan = Uang diambil Owner (Pribadi).
        });

        return back()->with('success', 'Transaksi berhasil disimpan!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        // Filter Tab (Default: Piutang/Kasbon)
        $type = $request->query('type', 'receivable'); 
        
        $debts = Debt::where('type', $type)
            ->latest()
            ->paginate(10);

        return view('debts.index', compact('debts', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:receivable,payable',
            'name' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'nullable|date',
        ]);

        Debt::create($request->all());

        return redirect()->back()->with('success', 'Data utang piutang berhasil dicatat!');
    }

    // Halaman Detail & Bayar Cicilan
    public function show($id)
    {
        $debt = Debt::with('payments')->findOrFail($id);
        return view('debts.show', compact('debt'));
    }

    // Proses Bayar Cicilan
    public function addPayment(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        
        $debt = Debt::findOrFail($id);

        if ($request->amount > $debt->remaining) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa hutang!');
        }

        DB::transaction(function () use ($request, $debt) {
            // 1. Catat Pembayaran
            DebtPayment::create([
                'debt_id' => $debt->id,
                'amount' => $request->amount,
                'date' => now(),
            ]);

            // 2. Update Header Hutang
            $debt->paid_amount += $request->amount;
            
            // Cek Status
            if ($debt->paid_amount >= $debt->amount) {
                $debt->status = 'paid';
            } else {
                $debt->status = 'partial';
            }
            
            $debt->save();
        });

        return back()->with('success', 'Pembayaran cicilan berhasil dicatat!');
    }

    public function destroy($id)
    {
        Debt::findOrFail($id)->delete();
        return redirect()->route('debts.index')->with('success', 'Data dihapus!');
    }
}
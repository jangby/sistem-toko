<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        // Ambil semua produk yang stoknya > 0
        $products = Product::where('stock', '>', 0)->latest()->get();
        return view('transactions.index', compact('products'));
    }

    public function store(Request $request)
    {
        // Validasi Data
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'exists:products,id',
            'pay_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi database (agar aman)

            // 1. Hitung Total Server Side (Biar tidak dicurangi di frontend)
            $total = 0;
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                $total += $product->sell_price * $item['qty'];

                // Cek stok lagi
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak cukup!");
                }

                // Kurangi Stok
                $product->decrement('stock', $item['qty']);
            }

            // 2. Simpan Transaksi Header
            $trx = Transaction::create([
                'invoice_no' => 'INV-' . date('YmdHis'),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $total,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);

            // 3. Simpan Detail
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                TransactionDetail::create([
                    'transaction_id' => $trx->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->sell_price,
                ]);
            }

            DB::commit(); // Simpan permanen

            return response()->json([
                'status' => 'success',
                'invoice' => $trx->invoice_no,
                'change' => number_format($trx->change_amount, 0, ',', '.'),
                'message' => 'Transaksi Berhasil!'
            ]);

        } catch (\Exception $e) {
            DB::rollback(); // Batalkan semua jika ada error
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
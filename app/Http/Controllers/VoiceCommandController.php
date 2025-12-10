<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VoiceCommandController extends Controller
{
    // API untuk mencari produk berdasarkan suara (Search Helper)
    public function searchProduct(Request $request)
    {
        $keyword = $request->input('keyword');
        // Hapus kata sambung umum
        $clean = str_replace(['jual', 'cari', 'barang', 'tolong'], '', strtolower($keyword));
        $clean = trim($clean);

        $product = Product::where('name', 'like', "%{$clean}%")->first();

        if ($product) {
            return response()->json([
                'status' => 'found',
                'data' => $product
            ]);
        }

        return response()->json(['status' => 'not_found']);
    }

    // API Finalisasi Transaksi
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required|numeric',
            'pay_amount' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::find($request->product_id);
            $total = $product->sell_price * $request->qty;
            
            // Cek Stok
            if ($product->stock < $request->qty) {
                return response()->json(['status' => 'error', 'message' => 'Stok tidak cukup!']);
            }

            // Kurangi Stok
            $product->decrement('stock', $request->qty);

            // Simpan Transaksi
            $trx = Transaction::create([
                'invoice_no' => 'VC-' . date('ymdHis'),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $total,
                'payment_method' => 'cash',
            ]);

            TransactionDetail::create([
                'transaction_id' => $trx->id,
                'product_id' => $product->id,
                'qty' => $request->qty,
                'price' => $product->sell_price,
            ]);

            DB::commit();

    // AMBIL DATA LENGKAP UNTUK DIKIRIM KE APLIKASI
    // Kita load relasi detail produk dan kasir
    $fullTrx = Transaction::with(['details.product', 'cashier'])->find($trx->id);

    return response()->json([
        'status' => 'success',
        'message' => "Transaksi sukses! Kembalian " . number_format($trx->change_amount),
        // Kita kirim objek lengkap ini ke Frontend
        'trx_data' => $fullTrx 
    ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
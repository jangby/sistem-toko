<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\WaService;
use App\Models\Setting; // <--- JANGAN LUPA

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
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'exists:products,id',
            'pay_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $lowStockItems = []; // Array penampung barang kritis

            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                
                // Cek stok cukup atau tidak
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak cukup!");
                }
                
                $total += $product->sell_price * $item['qty'];
                
                // 1. KURANGI STOK
                $product->decrement('stock', $item['qty']);

                // 2. CEK APAKAH STOK MENIPIS? (LOGIKA UTAMA)
                // Kita cek stok TERBARU setelah dikurangi
                if ($product->stock <= $product->min_stock) {
                    $lowStockItems[] = [
                        'name' => $product->name,
                        'stock' => $product->stock,
                        'unit' => $product->unit
                    ];
                }
            }

            // Simpan Transaksi Header
            $trx = Transaction::create([
                'invoice_no' => 'INV-' . date('YmdHis'),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $total,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);

            // Simpan Detail
            foreach ($request->cart as $item) {
                $product = Product::find($item['id']);
                TransactionDetail::create([
                    'transaction_id' => $trx->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->sell_price,
                ]);
            }

            DB::commit();

            // --- TAMBAHAN BARU: CEK SETTING NOTIFIKASI TRANSAKSI ---
            $isNotifEnabled = Setting::get('wa_notification_trx', '0'); // Default 0 (Mati)

            if ($isNotifEnabled == '1') {
                // Susun Pesan Struk Digital
                $msg  = "🔔 *TRANSAKSI BARU* 🔔\n";
                $msg .= "🧾 No: {$trx->invoice_no}\n";
                $msg .= "👤 Kasir: " . Auth::user()->name . "\n";
                $msg .= "🕒 Waktu: " . date('H:i') . "\n";
                $msg .= "----------------------------------\n";
                
                foreach ($request->cart as $item) {
                    $prod = Product::find($item['id']);
                    // Format: 2x Indomie (Rp 6.000)
                    $subtotal = number_format($prod->sell_price * $item['qty'], 0, ',', '.');
                    $msg .= "{$item['qty']}x {$prod->name} ({$subtotal})\n";
                }
                
                $msg .= "----------------------------------\n";
                $msg .= "💵 *TOTAL: Rp " . number_format($total, 0, ',', '.') . "*\n";
                $msg .= "💰 Bayar: Rp " . number_format($request->pay_amount, 0, ',', '.') . "\n";
                
                // Kirim (Fire and Forget agar tidak loading lama)
                try {
                    WaService::sendGroupMessage($msg);
                } catch (\Exception $e) {
                    // Silent fail (jangan ganggu kasir jika WA error)
                }
            }
            // -------------------------------------------------------

            // 3. KIRIM NOTIFIKASI WA (JIKA ADA BARANG KRITIS)
            if (!empty($lowStockItems)) {
                
                // Susun Pesan WA yang Rapi
                $message = "⚠️ *PERINGATAN STOK MENIPIS* ⚠️\n\n";
                $message .= "Ada transaksi baru ({$trx->invoice_no}), dan stok barang berikut menjadi kritis:\n";
                $message .= "----------------------------------\n";
                
                foreach ($lowStockItems as $item) {
                    $message .= "📦 *{$item['name']}*\n";
                    $message .= "   Sisa: {$item['stock']} {$item['unit']}\n";
                }
                
                $message .= "----------------------------------\n";
                $message .= "Mohon segera lakukan restock.";

                // Panggil Service WAHA (Jalankan di background/queue sebaiknya, tapi langsung juga oke untuk skala kecil)
                try {
                    WaService::sendGroupMessage($message);
                } catch (\Exception $e) {
                    // Jangan sampai error WA menggagalkan transaksi kasir
                    Log::error("Gagal kirim WA: " . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'invoice' => $trx->invoice_no,
                'change' => number_format($trx->change_amount, 0, ',', '.'),
                'message' => 'Transaksi Berhasil!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
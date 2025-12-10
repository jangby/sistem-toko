<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\WaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VoiceCommandController extends Controller
{
    public function process(Request $request)
    {
        $text = strtolower($request->input('text')); // Contoh: "jual 5 kopi kapal api"

        // 1. DETEKSI JUMLAH (Cari angka dalam kalimat)
        // Regex untuk mencari angka (misal: '5', '10', 'dua')
        preg_match('/\d+/', $text, $matches);
        $qty = $matches[0] ?? 1; // Default 1 jika tidak sebut angka

        // 2. DETEKSI NAMA BARANG
        // Hapus kata perintah umum agar sisa nama barangnya saja
        $keywordsToRemove = ['jual', 'beli', 'tolong', 'masukkan', 'input', 'lapor', 'barang', 'buah', 'pcs', 'bungkus', $qty];
        $cleanName = str_replace($keywordsToRemove, '', $text);
        $cleanName = trim($cleanName); // Sisa: "kopi kapal api"

        // 3. CARI DI DATABASE
        $product = Product::where('name', 'like', "%{$cleanName}%")->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => "Maaf, barang bernama '{$cleanName}' tidak ditemukan."
            ]);
        }

        // 4. PROSES TRANSAKSI
        try {
            DB::beginTransaction();

            if ($product->stock < $qty) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Stok {$product->name} kurang. Sisa: {$product->stock}"
                ]);
            }

            // Kurangi Stok
            $product->decrement('stock', $qty);

            // Buat Transaksi Otomatis
            $total = $product->sell_price * $qty;
            $trx = Transaction::create([
                'invoice_no' => 'VOICE-' . date('ymdHis'),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'pay_amount' => $total,
                'change_amount' => 0,
                'payment_method' => 'cash',
            ]);

            TransactionDetail::create([
                'transaction_id' => $trx->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $product->sell_price,
            ]);

            DB::commit();

            // Kirim WA (Optional)
            // WaService::sendGroupMessage("🎤 Voice Sale: {$qty}x {$product->name}");

            return response()->json([
                'status' => 'success',
                'message' => "Siap! {$qty} {$product->name} berhasil terjual. Total Rp " . number_format($total)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => "Terjadi kesalahan sistem."
            ]);
        }
    }
}
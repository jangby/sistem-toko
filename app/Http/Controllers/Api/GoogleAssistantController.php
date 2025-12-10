<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleAssistantController extends Controller
{
    public function handle(Request $request)
    {
        // Ambil Parameter dari Dialogflow
        $params = $request->input('queryResult.parameters');
        $intent = $request->input('queryResult.intent.displayName');

        // Pastikan intent-nya benar
        if ($intent == 'Input Penjualan') {
            return $this->prosesPenjualan($params);
        }

        return response()->json([
            'fulfillmentText' => 'Maaf, saya tidak mengerti perintah itu.'
        ]);
    }

    private function prosesPenjualan($params)
    {
        $namaBarang = $params['barang']; // Dari Dialogflow
        $qty = (int) $params['jumlah'];  // Dari Dialogflow

        // 1. Cari Barang di Database (Pencarian Mirip/Like)
        $product = Product::where('name', 'like', '%' . $namaBarang . '%')->first();

        if (!$product) {
            return response()->json([
                'fulfillmentText' => "Maaf bos, barang bernama $namaBarang tidak ditemukan di sistem."
            ]);
        }

        if ($product->stock < $qty) {
            return response()->json([
                'fulfillmentText' => "Gagal. Stok $product->name sisa $product->stock, tidak cukup untuk jual $qty."
            ]);
        }

        // 2. Proses Transaksi Otomatis
        try {
            DB::beginTransaction();

            $total = $product->sell_price * $qty;

            // Kurangi Stok
            $product->decrement('stock', $qty);

            // Buat Transaksi
            $trx = Transaction::create([
                'invoice_no' => 'VOICE-' . date('ymdHis'), // Kode khusus Voice
                'user_id' => 1, // Anggap Admin (ID 1)
                'total_amount' => $total,
                'pay_amount' => $total, // Anggap uang pas
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

            // 3. Respon Suara Balikan ke Google
            $msg = "Siap! Penjualan $qty $product->name senilai Rp " . number_format($total) . " berhasil disimpan. Stok sisa " . $product->stock . ".";

            return response()->json([
                'fulfillmentText' => $msg
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'fulfillmentText' => "Terjadi error sistem saat menyimpan data."
            ]);
        }
    }
}
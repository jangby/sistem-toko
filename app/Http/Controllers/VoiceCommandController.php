<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Debt;
use App\Models\Saving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VoiceCommandController extends Controller
{
    public function process(Request $request)
    {
        $text = strtolower($request->input('text')); // Contoh input: "cek stok indomie"

        // --- ROUTER PERINTAH ---

        // 1. CEK STOK
        if ($this->contains($text, ['cek stok', 'sisa stok', 'stok', 'ada gak', 'ada tidak', 'tersedia'])) {
            return $this->checkStock($text);
        }

        // 2. CEK UTANG / PIUTANG
        if ($this->contains($text, ['utang', 'piutang', 'kasbon', 'tagihan'])) {
            return $this->checkDebt($text);
        }

        // 3. CEK TABUNGAN / SIMPANAN
        if ($this->contains($text, ['tabungan', 'simpanan', 'dana darurat', 'uang simpanan'])) {
            return $this->checkSavings();
        }

        // 4. TRANSAKSI PENJUALAN (Default Action)
        return $this->processTransaction($text);
    }

    // --- LOGIKA PERINTAH ---

    private function checkStock($text)
    {
        // Bersihkan kata perintah, sisakan nama barang
        $keywords = ['cek stok', 'cek', 'stok', 'sisa', 'berapa', 'barang', 'ada gak', 'ada tidak', 'apakah'];
        $cleanName = str_replace($keywords, '', $text);
        $cleanName = trim($cleanName);

        if (empty($cleanName)) {
            return response()->json(['status' => 'info', 'message' => "Sebutkan nama barangnya."]);
        }

        $product = Product::where('name', 'like', "%{$cleanName}%")->first();

        if ($product) {
            $msg = "Stok {$product->name} sisa {$product->stock} {$product->unit}. Harga jual " . $this->bacaUang($product->sell_price);
            return response()->json(['status' => 'success', 'message' => $msg]);
        } else {
            return response()->json(['status' => 'error', 'message' => "Barang {$cleanName} tidak ditemukan."]);
        }
    }

    private function checkDebt($text)
    {
        // Hitung Piutang (Orang ngutang ke kita)
        $receivable = Debt::where('type', 'receivable')->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount'));
        
        // Hitung Utang (Kita ngutang ke orang)
        $payable = Debt::where('type', 'payable')->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount'));

        if (str_contains($text, 'utang')) {
            $msg = "Sisa utang toko anda adalah " . $this->bacaUang($payable);
        } elseif (str_contains($text, 'piutang') || str_contains($text, 'kasbon')) {
            $msg = "Total piutang atau kasbon pelanggan adalah " . $this->bacaUang($receivable);
        } else {
            $msg = "Total piutang " . $this->bacaUang($receivable) . ". Dan utang toko " . $this->bacaUang($payable);
        }

        return response()->json(['status' => 'success', 'message' => $msg]);
    }

    private function checkSavings()
    {
        $deposit = Saving::where('type', 'deposit')->sum('amount');
        $withdraw = Saving::where('type', 'withdrawal')->sum('amount');
        $balance = $deposit - $withdraw;

        return response()->json([
            'status' => 'success', 
            'message' => "Saldo tabungan toko saat ini adalah " . $this->bacaUang($balance)
        ]);
    }

    private function processTransaction($text)
    {
        // 1. Ambil Angka (Jumlah)
        preg_match('/\d+/', $text, $matches);
        $qty = $matches[0] ?? 1;

        // 2. Ambil Nama Barang
        $keywords = ['jual', 'beli', 'input', 'masukkan', 'buah', 'pcs', 'bungkus', 'kilo', $qty];
        $cleanName = str_replace($keywords, '', $text);
        $cleanName = trim($cleanName);

        $product = Product::where('name', 'like', "%{$cleanName}%")->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => "Barang tidak ditemukan."]);
        }

        if ($product->stock < $qty) {
            return response()->json(['status' => 'error', 'message' => "Stok kurang. Sisa {$product->stock}"]);
        }

        // 3. Eksekusi
        try {
            DB::beginTransaction();
            
            $product->decrement('stock', $qty);
            $total = $product->sell_price * $qty;

            $trx = Transaction::create([
                'invoice_no' => 'VC-' . date('ymdHis'),
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'pay_amount' => $total, // Asumsi uang pas
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

            // Load Data lengkap untuk Struk
            $fullTrx = Transaction::with(['details.product', 'cashier'])->find($trx->id);

            return response()->json([
                'status' => 'success',
                'message' => "Oke, {$qty} {$product->name} terjual. Total " . $this->bacaUang($total),
                'trx_data' => $fullTrx // Kirim data struk ke JS
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => "Gagal memproses transaksi."]);
        }
    }

    // Helpers
    private function contains($str, array $arr)
    {
        foreach($arr as $a) {
            if (stripos($str, $a) !== false) return true;
        }
        return false;
    }

    private function bacaUang($number)
    {
        if ($number >= 1000000) {
            return round($number/1000000, 1) . " juta rupiah";
        } elseif ($number >= 1000) {
            return round($number/1000) . " ribu rupiah";
        }
        return $number . " rupiah";
    }
}
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
use Carbon\Carbon;

class VoiceCommandController extends Controller
{
    public function process(Request $request)
    {
        $text = strtolower($request->input('text'));

        // --- ROUTER PERINTAH ---

        // 1. CEK KEUNTUNGAN / LABA (FITUR BARU)
        if ($this->contains($text, ['untung', 'keuntungan', 'laba', 'profit'])) {
            return $this->checkProfit($text);
        }

        // 2. CEK STOK
        if ($this->contains($text, ['cek stok', 'sisa stok', 'stok', 'ada gak', 'ada tidak', 'tersedia'])) {
            return $this->checkStock($text);
        }

        // 3. CEK UTANG / PIUTANG
        if ($this->contains($text, ['utang', 'piutang', 'kasbon', 'tagihan'])) {
            return $this->checkDebt($text);
        }

        // 4. CEK TABUNGAN
        if ($this->contains($text, ['tabungan', 'simpanan', 'dana darurat'])) {
            return $this->checkSavings();
        }

        // 5. TRANSAKSI (Default)
        return $this->processTransaction($text);
    }

    // --- LOGIKA BARU: CEK KEUNTUNGAN ---
    private function checkProfit($text)
    {
        // 1. Tentukan Rentang Waktu (Date Parsing)
        $dateInfo = $this->parseDateFromText($text);
        $startDate = $dateInfo['start'];
        $endDate = $dateInfo['end'];
        $periodName = $dateInfo['label'];

        // 2. Cek apakah ada nama barang spesifik?
        // Hapus kata kunci umum untuk mencari nama barang
        $keywords = ['berapa', 'keuntungan', 'laba', 'profit', 'penjualan', 'hari', 'ini', 'kemarin', 'bulan', 'tahun', 'tanggal', 'dari', 'sampai', 'pada'];
        
        // Hapus juga nama bulan dari text agar tidak dianggap nama barang
        $months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
        
        $cleanText = str_replace(array_merge($keywords, $months), '', $text);
        // Hapus angka (tanggal/tahun)
        $cleanText = preg_replace('/\d+/', '', $cleanText);
        $productName = trim($cleanText);

        // 3. Query Database
        $query = TransactionDetail::with('product')
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });

        // Jika ada nama barang spesifik
        $isSpecificProduct = false;
        if (!empty($productName) && strlen($productName) > 2) {
            // Cari ID produk dulu
            $product = Product::where('name', 'like', "%{$productName}%")->first();
            if ($product) {
                $query->where('product_id', $product->id);
                $productName = $product->name; // Gunakan nama asli dari DB
                $isSpecificProduct = true;
            }
        }

        $details = $query->get();

        // 4. Hitung Profit
        // Rumus: (Harga Jual saat transaksi - Harga Beli Master) * Qty
        // Catatan: Idealnya harga beli dicatat di history transaksi, tapi jika belum ada, kita pakai harga beli master saat ini.
        $totalProfit = 0;
        $totalQty = 0;

        foreach ($details as $item) {
            // Profit per item = Harga Jual (di struk) - Harga Modal (di data barang)
            $margin = $item->price - $item->product->buy_price;
            $totalProfit += ($margin * $item->qty);
            $totalQty += $item->qty;
        }

        // 5. Susun Kalimat Jawaban
        if ($totalQty == 0) {
            return response()->json(['status' => 'success', 'message' => "Belum ada penjualan {$periodName}."]);
        }

        $profitText = $this->bacaUang($totalProfit);
        
        if ($isSpecificProduct) {
            $msg = "Keuntungan penjualan {$productName} {$periodName} adalah {$profitText}. Terjual {$totalQty} pcs.";
        } else {
            $msg = "Total keuntungan toko {$periodName} adalah {$profitText}. Dari {$totalQty} barang terjual.";
        }

        return response()->json(['status' => 'success', 'message' => $msg]);
    }

    // --- HELPER PARSING TANGGAL CANGGIH ---
    private function parseDateFromText($text)
    {
        $dt = Carbon::now();

        // 1. Kemarin
        if (str_contains($text, 'kemarin')) {
            return [
                'start' => Carbon::yesterday()->startOfDay(), 
                'end' => Carbon::yesterday()->endOfDay(), 
                'label' => 'kemarin'
            ];
        }

        // 2. Bulan Kemarin
        if (str_contains($text, 'bulan lalu') || str_contains($text, 'bulan kemarin')) {
            return [
                'start' => Carbon::now()->subMonth()->startOfMonth(), 
                'end' => Carbon::now()->subMonth()->endOfMonth(), 
                'label' => 'bulan lalu'
            ];
        }

        // 3. Bulan Ini
        if (str_contains($text, 'bulan ini')) {
            return [
                'start' => Carbon::now()->startOfMonth(), 
                'end' => Carbon::now()->endOfMonth(), 
                'label' => 'bulan ini'
            ];
        }

        // 4. Deteksi Tanggal Spesifik (e.g., "1 desember 2025")
        // Regex: Angka(1-2 digit) Spasi Kata(Bulan) Spasi Angka(4 digit)
        if (preg_match('/(\d{1,2})\s+([a-z]+)\s+(\d{4})/', $text, $matches)) {
            $day = $matches[1];
            $monthName = $matches[2];
            $year = $matches[3];
            
            $monthMap = [
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 
                'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8, 
                'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];

            if (isset($monthMap[$monthName])) {
                $month = $monthMap[$monthName];
                $date = Carbon::create($year, $month, $day);
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => "tanggal $day $monthName $year"
                ];
            }
        }

        // 5. Default: HARI INI
        return [
            'start' => Carbon::today()->startOfDay(), 
            'end' => Carbon::today()->endOfDay(), 
            'label' => 'hari ini'
        ];
    }

    // --- LOGIKA LAINNYA (SAMA SEPERTI SEBELUMNYA) ---

    private function checkStock($text)
    {
        $keywords = ['cek stok', 'cek', 'stok', 'sisa', 'berapa', 'barang', 'ada gak', 'ada tidak', 'apakah'];
        $cleanName = str_replace($keywords, '', $text);
        $cleanName = trim($cleanName);

        if (empty($cleanName)) return response()->json(['status' => 'info', 'message' => "Sebutkan nama barangnya."]);

        $product = Product::where('name', 'like', "%{$cleanName}%")->first();

        if ($product) {
            $msg = "Stok {$product->name} sisa {$product->stock} {$product->unit}.";
            return response()->json(['status' => 'success', 'message' => $msg]);
        } else {
            return response()->json(['status' => 'error', 'message' => "Barang {$cleanName} tidak ditemukan."]);
        }
    }

    private function checkDebt($text)
    {
        $receivable = Debt::where('type', 'receivable')->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount'));
        $payable = Debt::where('type', 'payable')->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount'));

        if (str_contains($text, 'utang')) {
            $msg = "Sisa utang toko anda adalah " . $this->bacaUang($payable);
        } elseif (str_contains($text, 'piutang') || str_contains($text, 'kasbon')) {
            $msg = "Total piutang pelanggan adalah " . $this->bacaUang($receivable);
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
        return response()->json(['status' => 'success', 'message' => "Saldo tabungan toko saat ini " . $this->bacaUang($balance)]);
    }

    private function processTransaction($text)
    {
        preg_match('/\d+/', $text, $matches);
        $qty = $matches[0] ?? 1;

        $keywords = ['jual', 'beli', 'input', 'masukkan', 'buah', 'pcs', 'bungkus', 'kilo', $qty];
        $cleanName = str_replace($keywords, '', $text);
        $cleanName = trim($cleanName);

        $product = Product::where('name', 'like', "%{$cleanName}%")->first();

        if (!$product) return response()->json(['status' => 'error', 'message' => "Barang tidak ditemukan."]);
        if ($product->stock < $qty) return response()->json(['status' => 'error', 'message' => "Stok kurang. Sisa {$product->stock}"]);

        try {
            DB::beginTransaction();
            $product->decrement('stock', $qty);
            $total = $product->sell_price * $qty;

            $trx = Transaction::create([
                'invoice_no' => 'VC-' . date('ymdHis'),
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

            $fullTrx = Transaction::with(['details.product', 'cashier'])->find($trx->id);
            return response()->json([
                'status' => 'success',
                'message' => "Oke, {$qty} {$product->name} terjual. Total " . $this->bacaUang($total),
                'trx_data' => $fullTrx
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => "Gagal memproses transaksi."]);
        }
    }

    private function contains($str, array $arr)
    {
        foreach($arr as $a) { if (stripos($str, $a) !== false) return true; }
        return false;
    }

    private function bacaUang($number)
    {
        if ($number >= 1000000) return round($number/1000000, 1) . " juta rupiah";
        if ($number >= 1000) return round($number/1000) . " ribu rupiah";
        return $number . " rupiah";
    }
}
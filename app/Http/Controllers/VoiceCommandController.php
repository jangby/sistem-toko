<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Debt;
use App\Models\Saving;
use App\Models\CashMutation;
use App\Services\WaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoiceCommandController extends Controller
{
    public function process(Request $request)
    {
        $text = strtolower($request->input('text'));

        // --- ROUTER PERINTAH (URUTAN PENTING) ---

        // 1. KIRIM LAPORAN KE WA
        if ($this->contains($text, ['kirim laporan', 'laporan wa', 'kirim wa'])) {
            return $this->sendReport($text);
        }

        // 2. MANAJEMEN SIMPANAN / TABUNGAN (Tambah/Ambil)
        if ($this->contains($text, ['tambah simpanan', 'tambah tabungan', 'ambil simpanan', 'ambil tabungan', 'tarik tabungan'])) {
            return $this->manageSavings($text);
        }

        // 3. TAMBAH KATEGORI BARU
        if ($this->contains($text, ['tambah kategori', 'buat kategori', 'bikin kategori'])) {
            return $this->addCategory($text);
        }

        // 4. CEK STOK MENIPIS / RESTOCK
        if ($this->contains($text, ['menipis', 'restok', 'habis', 'kulakan', 'belanja'])) {
            return $this->checkLowStock();
        }

        // 5. CEK HARGA JUAL
        if ($this->contains($text, ['harga jual', 'harganya', 'berapa harga'])) {
            return $this->checkPrice($text);
        }

        // 6. CEK SUPPLIER / KATEGORI BARANG
        if ($this->contains($text, ['supplier', 'pemasok', 'kategori', 'dari mana'])) {
            return $this->checkInfo($text);
        }

        // 7. CEK KEUNTUNGAN
        if ($this->contains($text, ['untung', 'keuntungan', 'laba', 'profit'])) {
            return $this->checkProfit($text);
        }

        // 8. CEK STOK SPESIFIK
        if ($this->contains($text, ['cek stok', 'sisa stok', 'ada gak', 'tersedia'])) {
            return $this->checkStock($text);
        }

        // 9. CEK UTANG / PIUTANG
        if ($this->contains($text, ['utang', 'piutang', 'kasbon', 'tagihan'])) {
            return $this->checkDebt($text);
        }

        // 10. CEK SALDO TABUNGAN
        if ($this->contains($text, ['cek tabungan', 'cek simpanan', 'total simpanan', 'dana darurat'])) {
            return $this->checkSavingsBalance();
        }

        // 11. TRANSAKSI (Default)
        return $this->processTransaction($text);
    }

    // =========================================================================
    // MODUL 1: PELAPORAN WA
    // =========================================================================
    private function sendReport($text)
    {
        $dateInfo = $this->parseDateFromText($text);
        $startDate = $dateInfo['start'];
        $endDate = $dateInfo['end'];
        $periodName = $dateInfo['label'];

        // Cek filter produk
        $productName = $this->extractProductName($text, ['kirim', 'laporan', 'penjualan', 'wa', 'ke', 'grup']);
        
        $query = TransactionDetail::with('product')
            ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });

        $title = "LAPORAN PENJUALAN";
        if ($productName) {
            $product = Product::where('name', 'like', "%{$productName}%")->first();
            if ($product) {
                $query->where('product_id', $product->id);
                $title .= " " . strtoupper($product->name);
            }
        }

        $omset = 0;
        $qty = 0;
        $details = $query->get();

        foreach($details as $d) {
            $omset += ($d->price * $d->qty);
            $qty += $d->qty;
        }

        // Susun Pesan WA
        $msg = "📊 *{$title}* 📊\n";
        $msg .= "📅 Periode: {$periodName}\n";
        $msg .= "--------------------------\n";
        $msg .= "📦 Terjual: {$qty} Pcs\n";
        $msg .= "💰 Omset: " . $this->bacaUang($omset) . "\n";
        $msg .= "--------------------------\n";
        $msg .= "Dikirim via Perintah Suara 🎤";

        // Kirim
        WaService::sendGroupMessage($msg);

        return response()->json(['status' => 'success', 'message' => "Laporan {$periodName} berhasil dikirim ke Grup WA."]);
    }

    // =========================================================================
    // MODUL 2: MANAJEMEN SIMPANAN (BANK vs LACI)
    // =========================================================================
    private function manageSavings($text)
    {
        // 1. Tentukan Tipe
        $type = (str_contains($text, 'tambah')) ? 'deposit' : 'withdrawal';
        
        // 2. Tentukan Sumber (Laci/Kas vs Manual/Bank)
        $source = 'manual'; // Default Luar
        if (str_contains($text, 'laci') || str_contains($text, 'kas')) {
            $source = 'cash';
        }

        // 3. Ambil Nominal
        $amount = $this->parseNominal($text);
        if ($amount <= 0) return response()->json(['status' => 'error', 'message' => "Sebutkan nominal uangnya."]);

        try {
            DB::beginTransaction();

            // Cek Saldo jika narik
            if ($type == 'withdrawal') {
                $current = Saving::where('type', 'deposit')->sum('amount') - Saving::where('type', 'withdrawal')->sum('amount');
                if ($amount > $current) return response()->json(['status' => 'error', 'message' => "Saldo tabungan tidak cukup."]);
            }

            Saving::create([
                'date' => now(),
                'type' => $type,
                'amount' => $amount,
                'source' => $source,
                'description' => 'Via Voice Command'
            ]);

            // Jika dari Laci & Nabung -> Potong Kas Toko
            if ($type == 'deposit' && $source == 'cash') {
                CashMutation::create([
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'amount' => $amount,
                    'description' => 'Setor ke Tabungan (Voice)',
                    'date' => now()
                ]);
            }
            
            // Jika Ambil Tabungan & Masuk Laci -> Tambah Kas Toko
            if ($type == 'withdrawal' && $source == 'cash') {
                CashMutation::create([
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'amount' => $amount,
                    'description' => 'Tarik dari Tabungan (Voice)',
                    'date' => now()
                ]);
            }

            DB::commit();
            $action = ($type == 'deposit') ? "disimpan" : "diambil";
            $from = ($source == 'cash') ? "dari kas toko" : "dari luar/bank";
            
            return response()->json(['status' => 'success', 'message' => "Oke, uang " . $this->bacaUang($amount) . " berhasil {$action} {$from}."]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => "Gagal memproses simpanan."]);
        }
    }

    // =========================================================================
    // MODUL 3: STOK MENIPIS
    // =========================================================================
    private function checkLowStock()
    {
        $products = Product::whereColumn('stock', '<=', 'min_stock')->take(5)->get();
        
        if ($products->isEmpty()) {
            return response()->json(['status' => 'success', 'message' => "Semua stok aman. Tidak ada yang perlu direstok."]);
        }

        $names = $products->pluck('name')->implode(', ');
        $count = $products->count();
        
        return response()->json(['status' => 'success', 'message' => "Ada {$count} barang menipis: {$names}. Segera lakukan restok."]);
    }

    // =========================================================================
    // MODUL 4: HARGA JUAL, SUPPLIER, KATEGORI
    // =========================================================================
    private function checkPrice($text)
    {
        $name = $this->extractProductName($text, ['berapa', 'harga', 'jual', 'harganya']);
        $product = Product::where('name', 'like', "%{$name}%")->first();
        if(!$product) return response()->json(['status' => 'error', 'message' => "Barang tidak ditemukan."]);
        
        return response()->json(['status' => 'success', 'message' => "Harga jual {$product->name} adalah " . $this->bacaUang($product->sell_price)]);
    }

    private function checkInfo($text)
    {
        $name = $this->extractProductName($text, ['siapa', 'apa', 'nama', 'supplier', 'pemasok', 'kategori', 'dari', 'mana']);
        $product = Product::with(['supplier', 'category'])->where('name', 'like', "%{$name}%")->first();
        if(!$product) return response()->json(['status' => 'error', 'message' => "Barang tidak ditemukan."]);

        $msg = "Barang {$product->name}. ";
        if (str_contains($text, 'supplier') || str_contains($text, 'pemasok') || str_contains($text, 'dari mana')) {
            $sup = $product->supplier ? $product->supplier->name : "Belum ada supplier";
            $msg .= "Suppliernya adalah {$sup}.";
        }
        if (str_contains($text, 'kategori')) {
            $cat = $product->category ? $product->category->name : "Tanpa kategori";
            $msg .= "Kategorinya adalah {$cat}.";
        }
        
        return response()->json(['status' => 'success', 'message' => $msg]);
    }

    private function addCategory($text)
    {
        // Text: "Tambah kategori baru Snack"
        $name = str_replace(['tambah', 'buat', 'bikin', 'kategori', 'baru', 'namanya'], '', $text);
        $name = trim(ucwords($name));

        if (empty($name)) return response()->json(['status' => 'error', 'message' => "Sebutkan nama kategorinya."]);

        Category::firstOrCreate(['name' => $name]);
        return response()->json(['status' => 'success', 'message' => "Kategori {$name} berhasil dibuat."]);
    }

    // =========================================================================
    // HELPER & UTILS
    // =========================================================================
    
    private function extractProductName($text, $keywordsToRemove)
    {
        $text = str_replace($keywordsToRemove, '', $text);
        // Hapus keterangan waktu
        $timeWords = ['hari', 'ini', 'kemarin', 'bulan', 'lalu', 'tahun', 'tanggal', 'desember', 'januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november'];
        $text = str_replace($timeWords, '', $text);
        return trim(preg_replace('/\d+/', '', $text));
    }

    private function parseNominal($text)
    {
        // Clean text
        $clean = str_replace(['rp', '.', ','], '', $text);
        
        // Cek angka explicit "50000"
        preg_match('/\d+/', $clean, $matches);
        $base = isset($matches[0]) ? (int)$matches[0] : 0;

        // Cek multiplier kata
        $multiplier = 1;
        if (str_contains($text, 'juta')) $multiplier = 1000000;
        elseif (str_contains($text, 'ribu')) $multiplier = 1000;
        elseif (str_contains($text, 'ratus')) $multiplier = 100;

        // Logic konversi kata ke angka (satu, dua...) ada di Frontend JS
        // Di sini kita asumsi frontend mengirim angka atau backend parse angka digit
        
        return $base * $multiplier;
    }

    // --- REUSED FUNCTIONS (Sama seperti sebelumnya) ---
    private function checkProfit($text) { /* ... Logika Profit sebelumnya ... */ 
        $dateInfo = $this->parseDateFromText($text);
        $startDate = $dateInfo['start']; $endDate = $dateInfo['end']; $periodName = $dateInfo['label'];
        
        $productName = $this->extractProductName($text, ['berapa', 'keuntungan', 'laba', 'profit', 'penjualan']);
        
        $query = TransactionDetail::with('product')->whereHas('transaction', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });

        $isSpec = false;
        if($productName && strlen($productName) > 2) {
            $prod = Product::where('name', 'like', "%{$productName}%")->first();
            if($prod) { $query->where('product_id', $prod->id); $productName = $prod->name; $isSpec = true; }
        }

        $details = $query->get();
        $profit = 0; $qty = 0;
        foreach($details as $item) { $profit += ($item->price - $item->product->buy_price) * $item->qty; $qty += $item->qty; }

        if($qty == 0) return response()->json(['status' => 'success', 'message' => "Belum ada data penjualan {$periodName}."]);
        
        $msg = $isSpec ? "Keuntungan {$productName} {$periodName} adalah " : "Total keuntungan {$periodName} adalah ";
        $msg .= $this->bacaUang($profit);
        return response()->json(['status' => 'success', 'message' => $msg]);
    }

    private function parseDateFromText($text) { /* ... Logika Date sebelumnya ... */ 
        if (str_contains($text, 'kemarin')) return ['start' => Carbon::yesterday()->startOfDay(), 'end' => Carbon::yesterday()->endOfDay(), 'label' => 'kemarin'];
        if (str_contains($text, 'bulan lalu') || str_contains($text, 'bulan kemarin')) return ['start' => Carbon::now()->subMonth()->startOfMonth(), 'end' => Carbon::now()->subMonth()->endOfMonth(), 'label' => 'bulan lalu'];
        if (str_contains($text, 'bulan ini')) return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth(), 'label' => 'bulan ini'];
        if (preg_match('/(\d{1,2})\s+([a-z]+)\s+(\d{4})/', $text, $matches)) {
            $map = ['januari'=>1,'februari'=>2,'maret'=>3,'april'=>4,'mei'=>5,'juni'=>6,'juli'=>7,'agustus'=>8,'september'=>9,'oktober'=>10,'november'=>11,'desember'=>12];
            if(isset($map[$matches[2]])) {
                $d = Carbon::create($matches[3], $map[$matches[2]], $matches[1]);
                return ['start' => $d->copy()->startOfDay(), 'end' => $d->copy()->endOfDay(), 'label' => "tanggal {$matches[1]} {$matches[2]}"];
            }
        }
        return ['start' => Carbon::today()->startOfDay(), 'end' => Carbon::today()->endOfDay(), 'label' => 'hari ini'];
    }

    // Default functions
    private function checkStock($text) {
        $name = $this->extractProductName($text, ['cek', 'stok', 'sisa', 'ada', 'gak']);
        $p = Product::where('name', 'like', "%{$name}%")->first();
        return $p ? response()->json(['status'=>'success', 'message'=>"Stok {$p->name} sisa {$p->stock} {$p->unit}."]) 
                  : response()->json(['status'=>'error', 'message'=>"Barang tidak ditemukan."]);
    }

    private function checkDebt($text) {
        $rec = Debt::where('type', 'receivable')->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount'));
        $pay = Debt::where('type', 'payable')->where('status', '!=', 'paid')->sum(DB::raw('amount - paid_amount'));
        $msg = (str_contains($text, 'utang')) ? "Utang toko {$this->bacaUang($pay)}" : "Piutang pelanggan {$this->bacaUang($rec)}";
        return response()->json(['status'=>'success', 'message'=>$msg]);
    }

    private function checkSavingsBalance() {
        $bal = Saving::where('type', 'deposit')->sum('amount') - Saving::where('type', 'withdrawal')->sum('amount');
        return response()->json(['status'=>'success', 'message'=>"Total simpanan {$this->bacaUang($bal)}"]);
    }

    private function processTransaction($text) {
        preg_match('/\d+/', $text, $matches); $qty = $matches[0] ?? 1;
        $name = $this->extractProductName($text, ['jual', 'beli', 'input', $qty]);
        $p = Product::where('name', 'like', "%{$name}%")->first();
        
        if(!$p) return response()->json(['status'=>'error', 'message'=>"Barang tidak ditemukan."]);
        if($p->stock < $qty) return response()->json(['status'=>'error', 'message'=>"Stok kurang."]);

        try {
            DB::beginTransaction();
            $p->decrement('stock', $qty);
            $total = $p->sell_price * $qty;
            $trx = Transaction::create(['invoice_no'=>'VC-'.date('ymdHis'), 'user_id'=>Auth::id(), 'total_amount'=>$total, 'pay_amount'=>$total, 'change_amount'=>0]);
            TransactionDetail::create(['transaction_id'=>$trx->id, 'product_id'=>$p->id, 'qty'=>$qty, 'price'=>$p->sell_price]);
            DB::commit();
            $fullTrx = Transaction::with(['details.product', 'cashier'])->find($trx->id);
            return response()->json(['status'=>'success', 'message'=>"Oke, {$qty} {$p->name} terjual.", 'trx_data'=>$fullTrx]);
        } catch (\Exception $e) { DB::rollback(); return response()->json(['status'=>'error', 'message'=>'Error system']); }
    }

    private function contains($str, array $arr) {
        foreach($arr as $a) { if (stripos($str, $a) !== false) return true; } return false;
    }

    private function bacaUang($number) {
        if ($number >= 1000000) return round($number/1000000, 1) . " juta";
        if ($number >= 1000) return round($number/1000) . " ribu";
        return $number;
    }
}
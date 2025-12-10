<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\CashMutation;
use App\Services\WaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendDailyReport extends Command
{
    /**
     * Nama perintah yang nanti diketik di terminal
     */
    protected $signature = 'app:daily-report';

    /**
     * Keterangan perintah
     */
    protected $description = 'Kirim laporan harian toko ke WhatsApp Grup';

    /**
     * Logika Utama
     */
    public function handle()
    {
        $this->info('Sedang menyiapkan data laporan...');

        $date = Carbon::today();
        $dateString = $date->translatedFormat('l, d F Y');

        // 1. HITUNG KEUANGAN HARI INI
        $omset = Transaction::whereDate('created_at', $date)->sum('total_amount');
        $trxCount = Transaction::whereDate('created_at', $date)->count();
        
        // Pemasukan & Pengeluaran Manual (Kas Toko)
        $manualIn = CashMutation::whereDate('date', $date)->where('type', 'in')->sum('amount');
        $manualOut = CashMutation::whereDate('date', $date)->where('type', 'out')->sum('amount');

        // Total Bersih (Cashflow hari ini)
        // Rumus: Uang Masuk (Jual + Manual) - Uang Keluar
        $netFlow = ($omset + $manualIn) - $manualOut;

        // 2. CARI PRODUK TERLARIS HARI INI
        $topProducts = TransactionDetail::select('product_id', DB::raw('sum(qty) as total_qty'))
            ->whereHas('transaction', function($q) use ($date) {
                $q->whereDate('created_at', $date);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 3. SUSUN PESAN WHATSAPP
        $message = "📊 *LAPORAN TUTUP TOKO* 📊\n";
        $message .= "📅 $dateString\n";
        $message .= "----------------------------------\n";
        
        $message .= "💰 *KEUANGAN*\n";
        $message .= "• Omset Penjualan: Rp " . number_format($omset, 0, ',', '.') . "\n";
        $message .= "• Transaksi: $trxCount Nota\n";
        $message .= "• Kas Masuk (Lain): Rp " . number_format($manualIn, 0, ',', '.') . "\n";
        $message .= "• Kas Keluar (Ops): Rp " . number_format($manualOut, 0, ',', '.') . "\n";
        $message .= "----------------------------------\n";
        $message .= "💵 *ARUS KAS BERSIH: Rp " . number_format($netFlow, 0, ',', '.') . "*\n\n";

        $message .= "🏆 *PRODUK TERLARIS HARI INI*\n";
        if ($topProducts->count() > 0) {
            foreach ($topProducts as $index => $item) {
                $num = $index + 1;
                $message .= "$num. {$item->product->name} ({$item->total_qty})\n";
            }
        } else {
            $message .= "_Belum ada penjualan hari ini_\n";
        }

        $message .= "----------------------------------\n";
        $message .= "Sistem Toko Otomatis 🤖";

        // 4. KIRIM VIA SERVICE
        $this->info('Mengirim ke WhatsApp...');
        
        $status = WaService::sendGroupMessage($message);

        if ($status) {
            $this->info('Laporan BERHASIL dikirim!');
        } else {
            $this->error('Laporan GAGAL dikirim. Cek log atau koneksi WAHA.');
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StockController extends Controller
{
    // Halaman Utama Stok
    public function index()
    {
        // 1. Barang Stok Menipis (Critical)
        $lowStocks = Product::whereColumn('stock', '<=', 'min_stock')->get();

        // 2. Riwayat Kulakan
        $purchases = Purchase::with('supplier')->latest()->paginate(10);

        return view('stocks.index', compact('lowStocks', 'purchases'));
    }

    // Halaman Buat Rencana Belanja (PO)
    public function create()
    {
        $suppliers = Supplier::all();

        // LOGIKA CERDAS: Ambil produk terlaris bulan ini untuk saran restok
        $recommendations = TransactionDetail::select('product_id', DB::raw('sum(qty) as sold'))
            ->whereMonth('created_at', date('m'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Rumus Saran: (Terjual Bulan Ini * 1.5) - Stok Saat Ini
                // Artinya kita stok untuk kebutuhan 1.5 bulan ke depan
                $suggestion = ceil($item->sold * 1.5) - $item->product->stock;
                $item->suggestion = $suggestion > 0 ? $suggestion : 10; // Minimal saran 10
                return $item;
            });

        // Ambil semua produk untuk manual search
        $allProducts = Product::select('id', 'name', 'stock', 'buy_price')->get();

        return view('stocks.create', compact('suppliers', 'recommendations', 'allProducts'));
    }

    // Simpan Rencana Belanja
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'items' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $po = Purchase::create([
                'po_number' => 'PO-' . date('YmdHis'),
                'supplier_id' => $request->supplier_id,
                'date' => date('Y-m-d'),
                'status' => 'pending',
                'total_estimated' => $request->total_estimated,
            ]);

            foreach ($request->items as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $po->id,
                    'product_id' => $item['id'],
                    'request_qty' => $item['qty'],
                    'received_qty' => 0, // Belum datang
                    'buy_price' => $item['price'],
                ]);
            }
        });

        return redirect()->route('stok.index')->with('success', 'Rencana pembelian dibuat!');
    }

    // Halaman Cek Barang Datang (Receiving)
    public function show($id)
    {
        $purchase = Purchase::with(['details.product', 'supplier'])->findOrFail($id);
        
        // Jika sudah selesai, tampilkan mode view saja (bukan edit)
        return view('stocks.receive', compact('purchase'));
    }

    // Proses Finalisasi Stok (Update Gudang)
    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status == 'completed') {
            return back()->with('error', 'Data ini sudah selesai diproses!');
        }

        DB::transaction(function () use ($request, $purchase) {
            foreach ($request->items as $itemData) {
                // Ambil detail pesanan
                $detail = PurchaseDetail::findOrFail($itemData['detail_id']);
                
                // Update data real yang diterima
                $detail->update([
                    'received_qty' => $itemData['received_qty'],
                    'buy_price' => $itemData['buy_price'], // Update harga beli jika berubah
                ]);

                // Update Master Barang (Tambah Stok & Update Harga Modal Baru)
                $product = Product::findOrFail($detail->product_id);
                $product->stock += $itemData['received_qty'];
                $product->buy_price = $itemData['buy_price']; // Harga modal diupdate ke harga terbaru
                $product->save();
            }

            // Tandai PO selesai
            $purchase->update(['status' => 'completed']);
        });

        return redirect()->route('stok.index')->with('success', 'Stok berhasil ditambahkan ke gudang!');
    }

    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product'])->findOrFail($id);

        // Load view khusus PDF (nanti kita buat)
        $pdf = Pdf::loadView('stocks.pdf', compact('purchase'));
        
        // Atur ukuran kertas A4 Potrait
        $pdf->setPaper('a4', 'portrait');

        // Download file dengan nama otomatis (misal: PO-20251210001.pdf)
        return $pdf->stream($purchase->po_number . '.pdf');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Pastikan Model Category ada
use App\Models\Supplier; // Pastikan Model Supplier ada
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Pencarian Sederhana
        $query = Product::with(['category', 'supplier'])->latest();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20); // Tampilkan 20 per scroll
        
        // Kita butuh data kategori & supplier untuk dropdown di Modal
        $categories = Category::all(); 
        $suppliers = Supplier::all();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buy_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'stock' => 'required|integer',
            'min_stock' => 'required|integer',
            'unit' => 'required|string',
        ]);

        Product::create($validated);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|unique:products,barcode,' . $product->id,
            'category_id' => 'nullable',
            'supplier_id' => 'nullable',
            'buy_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'stock' => 'required|integer',
            'min_stock' => 'required|integer',
            'unit' => 'required|string',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Barang dihapus!');
    }
}
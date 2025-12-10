<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::latest();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(20);

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($request->all());

        return redirect()->back()->with('success', 'Kategori berhasil dibuat!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        // Opsional: Cek apakah kategori sedang dipakai barang?
        // Jika iya, sebaiknya jangan dihapus atau set null di barangnya.
        // Untuk sekarang kita hapus saja (Barang akan jadi uncategorized karena onDelete set null di migrasi).
        
        $category->delete();

        return redirect()->back()->with('success', 'Kategori dihapus!');
    }
}
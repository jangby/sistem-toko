<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20" 
         x-data="productApp()">
        
        <div class="bg-white dark:bg-gray-800 p-4 sticky top-0 z-10 shadow-sm rounded-b-2xl">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Data Barang</h1>
            </div>

            <form action="{{ route('products.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau barcode..." 
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-blue-500 dark:text-white text-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mx-4 mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-4 space-y-3">
            @forelse ($products as $product)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center group active:scale-95 transition-transform duration-100">
                    
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $product->stock <= $product->min_stock ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                Stok: {{ $product->stock }} {{ $product->unit }}
                            </span>
                            @if($product->category)
                                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded">{{ $product->category->name }}</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-800 dark:text-white truncate">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-400 mb-1">{{ $product->barcode ?? '-' }}</p>
                        <p class="text-blue-600 font-bold">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                    </div>

                    <button @click="openEditModal({{ $product }})" 
                            class="p-3 bg-gray-50 dark:bg-gray-700 rounded-full text-blue-600 hover:bg-blue-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-gray-500 text-sm">Belum ada barang.</p>
                </div>
            @endforelse

            <div class="mt-4 pb-20">
                {{ $products->links() }}
            </div>
        </div>

        <button @click="openCreate = true" 
                class="fixed bottom-6 right-6 lg:right-[calc(50%-12rem)] bg-blue-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-blue-700 hover:scale-110 transition z-40">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>


        <div x-show="openCreate" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             style="display: none;">
            
            <div @click.away="openCreate = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Barang</h2>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama Barang</label>
                        <input type="text" name="name" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Barcode/Kode</label>
                        <input type="text" name="barcode" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Harga Modal</label>
                            <input type="number" name="buy_price" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Harga Jual</label>
                            <input type="number" name="sell_price" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Stok</label>
                            <input type="number" name="stock" value="0" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Min. Alert</label>
                            <input type="number" name="min_stock" value="5" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Satuan</label>
                            <select name="unit" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                                <option value="dus">Dus</option>
                                <option value="kg">Kg</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Kategori</label>
                        <select name="category_id" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700">Simpan Barang</button>
                </form>
            </div>
        </div>


        <div x-show="openEdit" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             style="display: none;">
            
            <div @click.away="openEdit = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Edit Barang</h2>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form :action="updateUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama Barang</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Barcode</label>
                        <input type="text" name="barcode" x-model="editData.barcode" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Harga Modal</label>
                            <input type="number" name="buy_price" x-model="editData.buy_price" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Harga Jual</label>
                            <input type="number" name="sell_price" x-model="editData.sell_price" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Stok</label>
                            <input type="number" name="stock" x-model="editData.stock" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Min. Alert</label>
                            <input type="number" name="min_stock" x-model="editData.min_stock" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Satuan</label>
                            <select name="unit" x-model="editData.unit" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                                <option value="dus">Dus</option>
                                <option value="kg">Kg</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Kategori</label>
                        <select name="category_id" x-model="editData.category_id" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Update</button>
                    </div>
                </form>

                <form :action="deleteUrl" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 text-red-600 py-3 rounded-xl font-bold hover:bg-red-200">Hapus Barang</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function productApp() {
            return {
                openCreate: false,
                openEdit: false,
                editData: {},
                updateUrl: '',
                deleteUrl: '',

                openEditModal(product) {
                    // Isi data form edit dengan data produk yang diklik
                    this.editData = product;
                    
                    // Set URL untuk update dan delete (Ganti ID secara dinamis)
                    this.updateUrl = "{{ route('products.index') }}/" + product.id;
                    this.deleteUrl = "{{ route('products.index') }}/" + product.id;
                    
                    this.openEdit = true;
                }
            }
        }
    </script>
</x-app-layout>
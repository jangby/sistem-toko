<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative flex flex-col"
         x-data="poApp()">
        
        <div class="bg-white dark:bg-gray-800 p-4 shadow-sm z-10 sticky top-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('stok.index') }}" class="p-2 rounded-full hover:bg-gray-100"><svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></a>
                <h1 class="text-lg font-bold dark:text-white">Buat Pesanan Baru</h1>
            </div>
        </div>

        <div class="p-4 pb-32 flex-1 overflow-y-auto">
            <div class="mb-4">
                <label class="text-xs font-bold text-gray-500 mb-1 block">Pilih Supplier</label>
                <select x-model="supplierId" class="w-full rounded-xl border-gray-300 text-sm focus:ring-indigo-500">
                    <option value="">-- Pilih --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-2">Saran Pembelian (Top Sales)</h3>
                <div class="flex gap-2 overflow-x-auto pb-2">
                    @foreach($recommendations as $rec)
                        <div class="min-w-[160px] bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                            <div>
                                <p class="font-bold text-xs truncate">{{ $rec->product->name }}</p>
                                <p class="text-[10px] text-gray-400">Stok: {{ $rec->product->stock }} | Laris: {{ $rec->sold }}</p>
                            </div>
                            <button @click="addItem({{ $rec->product->id }}, '{{ $rec->product->name }}', {{ $rec->suggestion }}, {{ $rec->product->buy_price }})" 
                                    class="mt-2 w-full bg-indigo-50 text-indigo-700 text-[10px] font-bold py-1.5 rounded-lg hover:bg-indigo-100">
                                + Tambah {{ $rec->suggestion }} Pcs
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Daftar Belanjaan</h3>
            <div class="space-y-2">
                <template x-for="(item, index) in items" :key="index">
                    <div class="bg-white p-3 rounded-xl border border-gray-200 flex justify-between items-center">
                        <div class="flex-1">
                            <p class="font-bold text-sm" x-text="item.name"></p>
                            <p class="text-xs text-gray-400">Est. Harga: Rp <span x-text="item.price"></span></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="item.qty" class="w-16 p-1 text-center text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-indigo-500">
                            <button @click="removeItem(index)" class="text-red-400 hover:text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </div>
                    </div>
                </template>
                <div x-show="items.length === 0" class="text-center py-6 text-gray-400 text-xs">Belum ada barang dipilih.</div>
            </div>

            <div class="mt-4">
                <button @click="openSearch = true" class="w-full border-2 border-dashed border-gray-300 rounded-xl py-3 text-gray-500 font-bold text-sm hover:border-indigo-500 hover:text-indigo-500 transition">
                    + Cari Barang Lain Manual
                </button>
             </div>
        </div>

        <div class="fixed bottom-0 w-full max-w-md bg-white border-t p-4 z-20">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs text-gray-500">Estimasi Total</span>
                <span class="font-bold text-lg text-indigo-600" x-text="formatRupiah(calculateTotal())"></span>
            </div>
            <button @click="submitPO()" :disabled="items.length === 0 || !supplierId" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold disabled:bg-gray-300 transition shadow-lg shadow-indigo-500/30">
                Buat Surat Pesanan
            </button>
        </div>

        <div x-show="openSearch" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="openSearch = false" class="bg-white w-full max-w-sm rounded-2xl p-4 max-h-[80vh] overflow-y-auto">
                <input type="text" x-model="keyword" placeholder="Cari nama barang..." class="w-full rounded-lg border-gray-300 mb-3">
                <div class="space-y-2">
                    <template x-for="prod in filteredProducts">
                        <div @click="addItem(prod.id, prod.name, 10, prod.buy_price); openSearch = false" class="p-3 bg-gray-50 rounded-lg hover:bg-indigo-50 cursor-pointer">
                            <p class="font-bold text-sm" x-text="prod.name"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <script>
        function poApp() {
            return {
                supplierId: '',
                items: [],
                allProducts: @json($allProducts),
                keyword: '',
                openSearch: false,

                get filteredProducts() {
                    return this.allProducts.filter(p => p.name.toLowerCase().includes(this.keyword.toLowerCase())).slice(0, 10);
                },

                addItem(id, name, qty, price) {
                    // Cek duplikat
                    let exist = this.items.find(i => i.id === id);
                    if(exist) { exist.qty += parseInt(qty); }
                    else { this.items.push({ id: id, name: name, qty: parseInt(qty), price: parseFloat(price) }); }
                },

                removeItem(index) { this.items.splice(index, 1); },

                calculateTotal() { return this.items.reduce((acc, item) => acc + (item.qty * item.price), 0); },

                formatRupiah(number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number); },

                submitPO() {
                    fetch('{{ route("stok.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ supplier_id: this.supplierId, items: this.items, total_estimated: this.calculateTotal() })
                    }).then(() => window.location.href = '{{ route("stok.index") }}');
                }
            }
        }
    </script>
</x-app-layout>
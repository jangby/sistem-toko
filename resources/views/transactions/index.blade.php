<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative flex flex-col" 
         x-data="posApp()" 
         x-init="initScanner()">
        
        <div class="bg-white dark:bg-gray-800 p-4 sticky top-0 z-20 shadow-sm rounded-b-2xl">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white leading-none">Kasir</h1>
                    <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Scanner Ready
                    </p>
                </div>
                <div class="ml-auto">
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full border border-blue-200" x-text="cartTotalQty() + ' Item'">0 Item</span>
                </div>
            </div>

            <div class="relative group">
                <input type="text" x-model="keyword" placeholder="Cari nama barang..." 
                       class="w-full pl-10 pr-12 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-blue-500 dark:text-white text-sm transition-all"
                       id="searchInput">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                
                <div class="absolute right-3 top-2.5 bg-white dark:bg-gray-600 p-1 rounded-md shadow-sm">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
            </div>
        </div>

        <div class="flex-1 p-4 pb-40 overflow-y-auto">
            <div class="grid grid-cols-2 gap-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)" 
                         class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 cursor-pointer hover:border-blue-500 transition active:scale-95 relative overflow-hidden group">
                        
                        <h3 class="font-bold text-gray-800 dark:text-white text-sm leading-tight h-10 overflow-hidden" x-text="product.name"></h3>
                        <p class="text-[10px] text-gray-400 mb-2 truncate" x-text="product.barcode || 'No Barcode'"></p>
                        
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[10px] text-gray-400">Stok: <span x-text="product.stock"></span></p>
                                <p class="font-bold text-blue-600 text-sm" x-text="formatRupiah(product.sell_price)"></p>
                            </div>
                            <div class="w-8 h-8 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </div>

                        <template x-if="getItemQty(product.id) > 0">
                            <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] px-2 py-1 rounded-bl-lg font-bold shadow-sm">
                                <span x-text="getItemQty(product.id)"></span>x
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            
            <div x-show="filteredProducts.length === 0" class="text-center py-10 text-gray-400">
                <p>Barang tidak ditemukan.</p>
            </div>
        </div>

        <div x-show="cart.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             class="fixed bottom-0 w-full max-w-md bg-white dark:bg-gray-800 border-t dark:border-gray-700 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)] z-30 rounded-t-3xl p-5">
            
            <div class="mb-4 max-h-32 overflow-y-auto space-y-3 pr-1 custom-scrollbar">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex justify-between items-center text-sm group">
                        <div class="flex-1 truncate pr-2 text-gray-700 dark:text-gray-300">
                            <span class="font-bold text-blue-600 bg-blue-50 dark:bg-gray-700 px-1.5 rounded mr-1" x-text="item.qty + 'x'"></span>
                            <span x-text="item.name"></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-medium text-gray-800 dark:text-white" x-text="formatRupiah(item.sell_price * item.qty)"></span>
                            <button @click="removeFromCart(index)" class="text-gray-300 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-4 border-t border-dashed border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex-1">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide font-bold">Total Belanja</p>
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white" x-text="formatRupiah(cartTotal())"></h2>
                </div>
                <button @click="openCheckoutModal()" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 active:scale-95 transition flex items-center gap-2">
                    Bayar
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>

        <div x-show="openCheckout" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-md p-4" 
             x-transition:enter="transition ease-out duration-300"
             style="display: none;">
            
            <div @click.away="openCheckout = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl p-6 shadow-2xl overflow-hidden relative">
                
                <button @click="openCheckout = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="text-center mb-6">
                    <p class="text-sm text-gray-500">Total Tagihan</p>
                    <h1 class="text-4xl font-black text-gray-800 dark:text-white tracking-tight" x-text="formatRupiah(cartTotal())"></h1>
                </div>

                <div class="mb-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold">Rp</span>
                    </div>
                    <input type="number" x-model="payAmount" id="payInput"
                           class="w-full pl-10 pr-4 py-4 rounded-xl bg-gray-50 dark:bg-gray-900 border-2 border-transparent focus:border-blue-500 focus:bg-white dark:focus:bg-gray-800 font-bold text-lg dark:text-white transition" 
                           placeholder="Masukkan nominal...">
                </div>

                <div class="grid grid-cols-3 gap-2 mb-6">
                    <button @click="payAmount = cartTotal()" 
                            class="col-span-1 py-2 px-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold border border-green-200 hover:bg-green-200 transition">
                        Uang Pas
                    </button>
                    <button @click="payAmount = 10000" class="py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-200">10.000</button>
                    <button @click="payAmount = 20000" class="py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-200">20.000</button>
                    <button @click="payAmount = 50000" class="py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-200">50.000</button>
                    <button @click="payAmount = 100000" class="py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-200">100.000</button>
                    <button @click="payAmount = 200000" class="py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-200">200.000</button>
                </div>

                <div x-show="payAmount >= cartTotal()" class="mb-6 p-4 bg-blue-50 dark:bg-gray-700 rounded-2xl flex justify-between items-center animate-pulse-once">
                    <span class="text-blue-700 dark:text-blue-300 font-bold text-sm">Kembalian</span>
                    <span class="text-blue-700 dark:text-blue-300 font-black text-xl" x-text="formatRupiah(calculateChange())"></span>
                </div>

                <button @click="processPayment()" 
                        :disabled="payAmount < cartTotal() || processing"
                        class="w-full py-4 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:dark:bg-gray-700 disabled:cursor-not-allowed transition shadow-lg shadow-blue-500/30 flex justify-center items-center gap-2">
                    <span x-show="!processing">Proses Pembayaran</span>
                    <span x-show="processing" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
                </button>
            </div>
        </div>

        <audio id="beepSound" src="https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3"></audio>

    </div>

    <script>
        function posApp() {
            return {
                products: @json($products),
                keyword: '',
                cart: [],
                openCheckout: false,
                payAmount: '',
                processing: false,
                barcodeBuffer: '',
                lastInputTime: 0,

                initScanner() {
                    // Global Event Listener untuk Barcode Scanner
                    // Logic: Scanner dianggap mengetik sangat cepat (beda dengan manusia)
                    window.addEventListener('keydown', (e) => {
                        // Jangan scan jika sedang mengetik di input search atau input bayar manual
                        const activeTag = document.activeElement.tagName;
                        const activeId = document.activeElement.id;

                        // Jika sedang fokus di input search/bayar, biarkan user mengetik manual
                        if (activeTag === 'INPUT' && (activeId === 'searchInput' || activeId === 'payInput')) {
                            // Kecuali jika user menekan ENTER di search box, kita bisa cek apakah itu barcode
                            if(e.key === 'Enter' && activeId === 'searchInput') {
                                this.checkBarcode(this.keyword);
                                this.keyword = ''; // Reset search
                                e.preventDefault();
                            }
                            return; 
                        }

                        // Logic Scanner (Keyboard Emulator)
                        const currentTime = new Date().getTime();
                        
                        if (currentTime - this.lastInputTime > 100) {
                            this.barcodeBuffer = ''; // Reset buffer jika jeda terlalu lama (berarti ketikan manual)
                        }
                        
                        this.lastInputTime = currentTime;

                        if (e.key === 'Enter') {
                            if (this.barcodeBuffer.length > 0) {
                                this.checkBarcode(this.barcodeBuffer);
                                this.barcodeBuffer = '';
                            }
                        } else if (e.key.length === 1) { // Hanya ambil karakter printable
                            this.barcodeBuffer += e.key;
                        }
                    });
                },

                checkBarcode(code) {
                    // Cari produk berdasarkan barcode yang discan
                    const product = this.products.find(p => p.barcode === code);
                    
                    if (product) {
                        this.addToCart(product);
                        this.playSound(); // Bunyi Beep
                    } else {
                        // Optional: Beritahu jika barang tidak ada
                        // alert('Barang dengan kode ' + code + ' tidak ditemukan.');
                    }
                },

                playSound() {
                    const audio = document.getElementById('beepSound');
                    if(audio) {
                        audio.currentTime = 0;
                        audio.play().catch(e => console.log('Audio error:', e));
                    }
                },

                // Filter Produk Visual
                get filteredProducts() {
                    if (this.keyword === '') return this.products;
                    return this.products.filter(p => 
                        p.name.toLowerCase().includes(this.keyword.toLowerCase()) || 
                        (p.barcode && p.barcode.includes(this.keyword))
                    );
                },

                addToCart(product) {
                    if (product.stock <= 0) {
                        alert('Stok Habis!'); return;
                    }
                    let existingItem = this.cart.find(item => item.id === product.id);
                    if (existingItem) {
                        if (existingItem.qty >= product.stock) {
                            alert('Stok maksimal!'); return;
                        }
                        existingItem.qty++;
                    } else {
                        this.cart.push({ ...product, qty: 1 });
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                getItemQty(productId) {
                    let item = this.cart.find(i => i.id === productId);
                    return item ? item.qty : 0;
                },

                cartTotalQty() {
                    return this.cart.reduce((total, item) => total + item.qty, 0);
                },

                cartTotal() {
                    return this.cart.reduce((total, item) => total + (item.sell_price * item.qty), 0);
                },

                calculateChange() {
                    let total = this.cartTotal();
                    let pay = this.payAmount || 0;
                    return Math.max(0, pay - total);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                },

                openCheckoutModal() {
                    this.openCheckout = true;
                    // Auto focus ke input uang setelah delay sedikit (biar modal muncul dulu)
                    setTimeout(() => document.getElementById('payInput').focus(), 100);
                },

                processPayment() {
                    this.processing = true;

                    fetch('{{ route("transaksi.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cart: this.cart,
                            pay_amount: this.payAmount,
                            payment_method: 'cash'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.processing = false;
                        if (data.status === 'success') {
                            alert('Transaksi Berhasil!\nKembalian: ' + data.change);
                            this.cart = [];
                            this.payAmount = '';
                            this.openCheckout = false;
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(error => {
                        this.processing = false;
                        alert('Terjadi kesalahan sistem.');
                        console.error(error);
                    });
                }
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    </style>
</x-app-layout>
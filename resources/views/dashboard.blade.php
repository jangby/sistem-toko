<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-10">
        
        <div class="bg-white dark:bg-gray-800 pt-8 pb-4 px-6 rounded-b-[2.5rem] shadow-sm z-10 relative mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Selamat bekerja,</p>
                    <h1 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">{{ Auth::user()->name }}</h1>
                    <span class="inline-block mt-1 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-600 rounded-md">
                        {{ Auth::user()->role }}
                    </span>
                </div>
                <a href="{{ route('profile.edit') }}" class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 p-1 cursor-pointer hover:scale-105 transition">
                    <div class="h-full w-full rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </a>
            </div>
        </div>

        <div class="px-6 space-y-6">

            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/30 relative overflow-hidden transform transition hover:scale-[1.02]">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                
                <p class="text-blue-100 text-sm font-medium mb-1 relative z-10">Omset Hari Ini</p>
                <div class="flex items-baseline gap-1 relative z-10">
                    <span class="text-sm font-semibold">Rp</span>
                    <h2 class="text-4xl font-black tracking-tight">{{ number_format($omsetToday, 0, ',', '.') }}</h2>
                </div>
                
                <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between relative z-10">
                    <div class="text-xs text-blue-100">
                        {{ \Carbon\Carbon::now()->format('d F Y') }}
                    </div>
                    <div class="text-xs font-bold bg-white/20 px-2 py-1 rounded">
                        Live Update
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center items-center text-center group hover:border-purple-200 transition">
                    <div class="p-2 bg-purple-100 text-purple-600 rounded-full mb-2 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $trxToday }}</span>
                    <span class="text-xs text-gray-400">Transaksi Hari Ini</span>
                </div>

                <a href="{{ route('stok.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-center items-center text-center group hover:border-red-200 transition cursor-pointer">
                    <div class="p-2 {{ $lowStockCount > 0 ? 'bg-red-100 text-red-600 animate-pulse' : 'bg-green-100 text-green-600' }} rounded-full mb-2 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-800 dark:text-white' }}">{{ $lowStockCount }}</span>
                    <span class="text-xs text-gray-400">Stok Menipis</span>
                </a>
            </div>

            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Menu Utama</h3>
                
                <div class="grid grid-cols-4 gap-4">

                    <a href="{{ route('transaksi.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-blue-600 rounded-2xl shadow-lg shadow-blue-500/40 flex items-center justify-center mb-2 group-hover:scale-105 transition duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Kasir</span>
                    </a>

                    <a href="{{ route('products.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-indigo-50 transition">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Barang</span>
                    </a>

                    <a href="{{ route('stok.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-pink-50 transition">
                            <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Stok</span>
                    </a>

                    <a href="{{ route('kas.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-green-50 transition">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Keuangan</span>
                    </a>

                    <a href="{{ route('debts.index') }}" class="group flex flex-col items-center">
    <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-red-50 group-hover:scale-105 transition duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    </div>
    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Utang/Piutang</span>
</a>

<a href="{{ route('tabungan.index') }}" class="group flex flex-col items-center">
    <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-yellow-50 group-hover:scale-105 transition duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Simpanan</span>
</a>

                    <a href="{{ route('laporan.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-cyan-50 transition">
                            <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Laporan</span>
                    </a>

                    <a href="{{ route('categories.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-yellow-50 transition">
                            <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Kategori</span>
                    </a>

                    <a href="{{ route('suppliers.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-orange-50 transition">
                            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">Supplier</span>
                    </a>

                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('users.index') }}" class="col-span-1 group flex flex-col items-center">
                        <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-purple-50 transition">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">User</span>
                    </a>
                    @endif

                    <a href="{{ route('settings.wa') }}" class="group flex flex-col items-center">
    <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center mb-2 group-hover:bg-teal-50 group-hover:scale-105 transition duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </div>
    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Setting WA</span>
</a>

                </div>
            </div>

            <div class="pt-8 text-center pb-8">
                <p class="text-[10px] text-gray-300 dark:text-gray-600">POS System v1.0 • Laravel 11</p>
            </div>

        </div>
    </div>

    <div x-data="voiceBot()" class="fixed bottom-6 left-6 z-50">
    
    <button @click="startSession()" 
            class="w-16 h-16 rounded-full shadow-2xl flex items-center justify-center transition transform hover:scale-110 border-4 border-white"
            :class="listening ? 'bg-red-500 animate-pulse' : 'bg-gradient-to-r from-emerald-500 to-teal-500'">
        <svg x-show="!listening" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
        <svg x-show="listening" class="w-8 h-8 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </button>

    <div x-show="message" x-transition class="absolute bottom-20 left-0 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-xl w-72 border border-gray-200 dark:border-gray-700">
        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Asisten Toko</p>
        <p class="text-sm font-bold text-gray-800 dark:text-white leading-relaxed" x-text="message"></p>
    </div>
</div>

<script>
    function voiceBot() {
        return {
            listening: false,
            message: '',
            step: 0, // 0:Idle, 1:Cari Barang, 2:Tanya Qty, 3:Tanya Uang
            data: { id: null, name: '', price: 0, qty: 0, pay: 0 },
            recognition: null,

            init() {
                if ('webkitSpeechRecognition' in window) {
                    this.recognition = new webkitSpeechRecognition();
                    this.recognition.continuous = false;
                    this.recognition.lang = 'id-ID';
                    
                    this.recognition.onstart = () => { this.listening = true; };
                    this.recognition.onend = () => { this.listening = false; };
                    
                    this.recognition.onresult = (event) => {
                        const text = event.results[0][0].transcript.toLowerCase();
                        this.processInput(text);
                    };
                }
            },

            // Mulai Percakapan
            startSession() {
                if(!this.recognition) this.init();
                
                if (this.step === 0) {
                    this.say("Sebutkan barang yang dijual.");
                    this.step = 1;
                }
                
                // Beri jeda agar komputer selesai bicara baru mendengarkan
                setTimeout(() => { this.recognition.start(); }, 1500);
            },

            // Otak Percakapan
            processInput(text) {
                console.log("Input: " + text + " | Step: " + this.step);

                // --- STEP 1: CARI BARANG ---
                if (this.step === 1) {
                    this.message = "Mencari: " + text + "...";
                    
                    // Cek jika user langsung sebut jumlah (misal: "2 Indomie")
                    let qtyMatch = text.match(/\d+/);
                    if(qtyMatch) this.data.qty = parseInt(qtyMatch[0]);

                    // Panggil API Search
                    fetch('{{ route("voice.search") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ keyword: text })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'found') {
                            this.data.id = res.data.id;
                            this.data.name = res.data.name;
                            this.data.price = res.data.sell_price;
                            
                            if (this.data.qty > 0) {
                                // Jika jumlah sudah disebut di awal
                                this.step = 3;
                                this.askPayment();
                            } else {
                                // Jika belum, tanya jumlah
                                this.step = 2;
                                this.say("Oke, " + res.data.name + ". Berapa jumlahnya?");
                                setTimeout(() => this.recognition.start(), 2000);
                            }
                        } else {
                            this.say("Barang tidak ditemukan. Coba lagi.");
                            setTimeout(() => this.recognition.start(), 2000);
                        }
                    });
                }

                // --- STEP 2: TANYA JUMLAH ---
                else if (this.step === 2) {
                    let number = this.parseNumber(text);
                    if (number > 0) {
                        this.data.qty = number;
                        this.step = 3;
                        this.askPayment();
                    } else {
                        this.say("Maaf, sebutkan angkanya saja. Berapa banyak?");
                        setTimeout(() => this.recognition.start(), 2000);
                    }
                }

                // --- STEP 3: TANYA PEMBAYARAN ---
                else if (this.step === 3) {
                    let total = this.data.price * this.data.qty;
                    
                    if (text.includes("pas") || text.includes("sama")) {
                        this.data.pay = total;
                    } else {
                        this.data.pay = this.parseMoney(text);
                    }

                    if (this.data.pay >= total) {
                        this.finalizeTransaction();
                    } else {
                        this.say("Uang kurang. Totalnya " + total + ". Uangnya berapa?");
                        setTimeout(() => this.recognition.start(), 3000);
                    }
                }
            },

            // Fungsi Helper: Tanya Uang
            askPayment() {
                let total = this.data.price * this.data.qty;
                this.say("Total " + total + ". Uangnya berapa?");
                setTimeout(() => this.recognition.start(), 2500);
            },

            // Fungsi Helper: Finalisasi
            finalizeTransaction() {
                this.say("Memproses transaksi...");
                
                fetch('{{ route("voice.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ 
                        product_id: this.data.id, 
                        qty: this.data.qty, 
                        pay_amount: this.data.pay 
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        this.say(res.message);
                        
                        // --- PERUBAHAN DISINI (KIRIM KE APP) ---
                        // Panggil fungsi untuk kirim data ke Aplikasi Android
                        this.sendToNativeApp(res.trx_data);
                        // ---------------------------------------

                        // Reset dan Refresh Dashboard
                        this.step = 0;
                        this.data = { id: null, name: '', price: 0, qty: 0, pay: 0 };
                        setTimeout(() => window.location.reload(), 3000); // Delay dikit biar suara selesai
                    } else {
                        this.say("Gagal. " + res.message);
                        this.step = 0;
                    }
                });
            },

            // --- FUNGSI JEMBATAN KE APLIKASI ANDROID/IOS ---
            sendToNativeApp(dataTransaksi) {
                // Ubah object jadi String JSON agar bisa dibaca Java/Kotlin/Swift
                const jsonString = JSON.stringify(dataTransaksi);

                try {
                    // Cek 1: Jika menggunakan Android JavascriptInterface
                    // Asumsi nama interface di Android Anda adalah 'AndroidPOS'
                    // dan nama fungsinya 'printStruk'
                    if (window.AndroidPOS && window.AndroidPOS.printStruk) {
                        window.AndroidPOS.printStruk(jsonString);
                        console.log("Data dikirim ke Android Interface");
                    } 
                    
                    // Cek 2: Jika menggunakan React Native WebView
                    else if (window.ReactNativeWebView) {
                        window.ReactNativeWebView.postMessage(jsonString);
                        console.log("Data dikirim ke React Native");
                    }

                    // Cek 3: Jika menggunakan Flutter (JavascriptChannel)
                    // Asumsi nama channelnya 'PrintChannel'
                    else if (window.PrintChannel) {
                        window.PrintChannel.postMessage(jsonString);
                        console.log("Data dikirim ke Flutter");
                    }

                    else {
                        console.log("Tidak terdeteksi di dalam aplikasi. Data Struk:", dataTransaksi);
                        alert("Mode Web: Simulasi Cetak Struk (Cek Console)");
                    }

                } catch (e) {
                    console.error("Gagal mengirim ke aplikasi:", e);
                }
            }

            // Fungsi Bicara (TTS)
            say(text) {
                this.message = text;
                let u = new SpeechSynthesisUtterance(text);
                u.lang = 'id-ID';
                u.rate = 1.1; // Sedikit lebih cepat
                window.speechSynthesis.speak(u);
            },

            // Parser Angka (Satu, Dua, 10, dll)
            parseNumber(text) {
                // Mapping manual angka text ke integer
                const map = { 'satu': 1, 'dua': 2, 'tiga': 3, 'empat': 4, 'lima': 5, 'enam': 6, 'tujuh': 7, 'delapan': 8, 'sembilan': 9, 'sepuluh': 10 };
                
                // Cek angka digit
                let match = text.match(/\d+/);
                if (match) return parseInt(match[0]);

                // Cek angka kata
                for (let k in map) {
                    if (text.includes(k)) return map[k];
                }
                return 0;
            },

            // Parser Uang (Ribu, Juta, Ceban, Goceng)
            parseMoney(text) {
                // Bersihkan text
                let clean = text.replace(/\./g, '').replace(/rp/g, '').trim();
                let num = this.parseNumber(clean);
                
                // Jika user bilang "Lima puluh ribu" -> parseNumber dapat 5 -> dikali 10000 jadi salah.
                // Logic simpel: Ambil semua angka digit
                let digitMatch = clean.match(/\d+/g);
                if (digitMatch) {
                    // Gabungkan angka (misal "50" dan "000")
                    return parseInt(digitMatch.join('')); 
                }
                
                // Logic Text to Money Sederhana
                let multiplier = 1;
                if (text.includes("ribu")) multiplier = 1000;
                if (text.includes("juta")) multiplier = 1000000;
                
                // Ambil angka depan (misal "lima" puluh ribu)
                let base = this.parseNumber(text); 
                // Jika parseNumber gagal (misal "20" tidak terdeteksi sbg kata "dua puluh")
                if(base === 0) {
                     // Fallback regex angka di depan kata ribu
                     let preMatch = text.match(/(\d+)\s*ribu/);
                     if(preMatch) base = parseInt(preMatch[1]);
                }

                if (base > 0) return base * multiplier;

                return 0; // Gagal parse
            }
        }
    }
</script>
</x-app-layout>
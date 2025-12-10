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

            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/30 relative overflow-hidden transform transition hover:scale-[1.02]">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                
                <p class="text-emerald-100 text-sm font-medium mb-1 relative z-10">Saldo Kas (Uang di Laci)</p>
                <div class="flex items-baseline gap-1 relative z-10">
                    <span class="text-sm font-semibold">Rp</span>
                    <h2 class="text-4xl font-black tracking-tight">{{ number_format($currentCashBalance, 0, ',', '.') }}</h2>
                </div>
                
                <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between relative z-10">
                    <div class="text-xs text-emerald-100">
                        Omset Hari Ini: <span class="font-bold">Rp {{ number_format($omsetToday, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-[10px] font-bold bg-white/20 px-2 py-1 rounded">
                        Live
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

    <div x-data="voiceApp()" class="fixed bottom-6 left-6 z-50">
    
    <button @click="toggleListening()" 
            class="w-16 h-16 rounded-full shadow-2xl flex items-center justify-center transition transform hover:scale-110 border-4 border-white"
            :class="isListening ? 'bg-red-500 animate-pulse' : 'bg-gradient-to-r from-emerald-500 to-teal-500'">
        
        <svg x-show="!isListening" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
        <svg x-show="isListening" class="w-8 h-8 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </button>

    <div x-show="message" 
         x-transition.opacity.duration.500ms
         class="absolute bottom-20 left-0 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-xl w-72 border border-gray-200 dark:border-gray-700 z-50">
        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Status Suara</p>
        <p class="text-sm font-bold text-gray-800 dark:text-white leading-relaxed" x-text="message"></p>
    </div>

</div>

<script>
    function voiceApp() {
        return {
            isListening: false,
            message: '',
            recognition: null,

            init() {
                if ('webkitSpeechRecognition' in window) {
                    this.recognition = new webkitSpeechRecognition();
                    this.recognition.continuous = false; // Stop otomatis setelah kalimat selesai
                    this.recognition.lang = 'id-ID';
                    this.recognition.interimResults = false;

                    this.recognition.onstart = () => {
                        this.isListening = true;
                        this.message = "Silakan bicara...";
                    };

                    this.recognition.onend = () => {
                        this.isListening = false;
                    };

                    this.recognition.onresult = (event) => {
                        const transcript = event.results[0][0].transcript;
                        this.message = 'Memproses: "' + transcript + '"';
                        this.sendToBackend(transcript);
                    };

                    this.recognition.onerror = (event) => {
                        this.isListening = false;
                        if(event.error == 'no-speech') {
                            this.message = "Tidak ada suara terdeteksi.";
                        } else {
                            this.message = "Error: " + event.error;
                        }
                    };
                } else {
                    alert("Gunakan Google Chrome untuk fitur suara.");
                }
            },

            toggleListening() {
                if (!this.recognition) this.init();
                
                if (this.isListening) {
                    this.recognition.stop();
                } else {
                    this.recognition.start();
                }
            },

            sendToBackend(text) {
                // Tampilkan loading visual
                this.message = "Sedang mencari data...";

                fetch('{{ route("voice.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ text: text })
                })
                .then(response => response.json())
                .then(data => {
                    this.message = data.message;
                    this.speak(data.message);

                    // JIKA ADA TRANSAKSI SUKSES -> KIRIM STRUK
                    if (data.status === 'success' && data.trx_data) {
                        this.sendToNativeApp(data.trx_data);
                        
                        // Refresh Dashboard setelah bicara selesai
                        setTimeout(() => window.location.reload(), 4000);
                    }
                })
                .catch(error => {
                    console.error(error);
                    this.message = "Gagal menghubungi server.";
                    this.speak("Maaf, terjadi kesalahan koneksi.");
                });
            },

            // --- TTS (Bicara) ---
            speak(text) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                window.speechSynthesis.speak(utterance);
            },

            // --- JEMBATAN KE APLIKASI (STRUK) ---
            sendToNativeApp(dataTransaksi) {
                const jsonString = JSON.stringify(dataTransaksi);
                try {
                    // Support Android Interface
                    if (window.AndroidPOS && window.AndroidPOS.printStruk) {
                        window.AndroidPOS.printStruk(jsonString);
                        console.log("Struk dikirim ke Android");
                    } 
                    // Support WebView Standard
                    else if (window.ReactNativeWebView) {
                        window.ReactNativeWebView.postMessage(jsonString);
                    }
                    else {
                        console.log("Mode Web: Struk tidak dicetak (Simulasi)");
                    }
                } catch (e) {
                    console.error("Gagal print:", e);
                }
            }
        }
    }
</script>
</x-app-layout>
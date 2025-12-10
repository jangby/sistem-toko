<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Animasi Melayang Halus */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Pattern Background Halus */
        .bg-pattern {
            background-color: #f0fdf4;
            background-image: radial-gradient(#15803d 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            opacity: 0.5;
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-white selection:bg-emerald-500 selection:text-white overflow-x-hidden">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-emerald-50/80 to-white"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-200/20 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-teal-200/20 rounded-full blur-[100px] translate-y-1/2 -translate-x-1/2"></div>
    </div>

    <nav class="relative z-50 w-full px-6 py-5 max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-600/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-emerald-950">TokoApp</span>
        </div>
        
        @if (Route::has('login'))
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-bold text-emerald-700 hover:text-emerald-800 transition">Dashboard &rarr;</a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-emerald-600 rounded-full hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
                        Masuk
                    </a>
                @endauth
            </div>
        @endif
    </nav>

    <main class="relative z-10 max-w-7xl mx-auto px-6 pt-8 pb-20 lg:pt-16 lg:flex lg:items-center lg:justify-between">
        
        <div class="lg:w-1/2 text-center lg:text-left mb-16 lg:mb-0">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100/50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Solusi UMKM Cerdas
            </div>
            
            <h1 class="text-4xl lg:text-6xl font-extrabold tracking-tight leading-[1.15] mb-6 text-slate-900">
                Kelola Toko Jadi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">Lebih Menguntungkan.</span>
            </h1>
            
            <p class="text-lg text-slate-500 mb-8 leading-relaxed max-w-lg mx-auto lg:mx-0">
                Aplikasi kasir yang membantu Anda mencatat penjualan, memantau stok, dan menganalisa keuntungan bisnis dengan mudah dari HP.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <a href="{{ route('login') }}" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-xl shadow-emerald-600/20 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    Mulai Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
                <a href="#fitur" class="px-8 py-4 bg-white hover:bg-emerald-50 text-slate-700 rounded-2xl font-bold border border-slate-200 shadow-sm transition flex items-center justify-center">
                    Pelajari Fitur
                </a>
            </div>
            
            <div class="mt-10 flex items-center justify-center lg:justify-start gap-6 opacity-70 grayscale hover:grayscale-0 transition duration-500">
               <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Terpercaya & Aman</div>
            </div>
        </div>

        <div class="lg:w-1/2 relative flex justify-center lg:justify-end">
            
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] bg-emerald-100 rounded-full blur-3xl opacity-50"></div>

            <div class="relative w-[300px] h-[600px] bg-white rounded-[3rem] border-8 border-slate-900 shadow-2xl shadow-emerald-900/10 animate-float z-20">
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-32 h-6 bg-slate-900 rounded-b-xl z-30"></div>
                
                <div class="w-full h-full bg-slate-50 rounded-[2.5rem] overflow-hidden relative flex flex-col">
                    
                    <div class="bg-emerald-600 h-36 pt-12 px-6 rounded-b-[2rem] shrink-0">
                        <div class="flex justify-between items-center mb-4">
                            <div class="w-10 h-10 bg-white/20 rounded-full backdrop-blur-sm"></div>
                            <div class="w-8 h-8 bg-white/20 rounded-full backdrop-blur-sm"></div>
                        </div>
                        <div class="h-4 w-24 bg-white/30 rounded mb-2"></div>
                        <div class="h-8 w-40 bg-white rounded-lg shadow-sm"></div>
                    </div>

                    <div class="p-5 -mt-6 flex-1 overflow-hidden">
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-4 flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="space-y-2 flex-1">
                                <div class="h-2 w-12 bg-slate-200 rounded"></div>
                                <div class="h-4 w-24 bg-slate-800 rounded"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 h-28 flex flex-col justify-center items-center gap-2">
                                <div class="w-10 h-10 bg-blue-100 rounded-full"></div>
                                <div class="h-2 w-12 bg-slate-200 rounded"></div>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 h-28 flex flex-col justify-center items-center gap-2">
                                <div class="w-10 h-10 bg-purple-100 rounded-full"></div>
                                <div class="h-2 w-12 bg-slate-200 rounded"></div>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 h-28 flex flex-col justify-center items-center gap-2">
                                <div class="w-10 h-10 bg-orange-100 rounded-full"></div>
                                <div class="h-2 w-12 bg-slate-200 rounded"></div>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 h-28 flex flex-col justify-center items-center gap-2">
                                <div class="w-10 h-10 bg-pink-100 rounded-full"></div>
                                <div class="h-2 w-12 bg-slate-200 rounded"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-t border-slate-100 h-16 flex justify-around items-center px-6 shrink-0">
                         <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                         </div>
                         <div class="w-8 h-8 rounded-lg bg-slate-100"></div>
                         <div class="w-8 h-8 rounded-lg bg-slate-100"></div>
                    </div>

                </div>
            </div>
            <div class="absolute top-32 -right-4 lg:-right-8 bg-white/90 backdrop-blur-md border border-slate-100 p-4 rounded-2xl shadow-xl animate-float" style="animation-delay: 1.5s;">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-100 w-10 h-10 rounded-full flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase">Stok</p>
                        <p class="font-bold text-slate-800">Terkendali</p>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-32 -left-4 lg:-left-8 bg-white/90 backdrop-blur-md border border-slate-100 p-4 rounded-2xl shadow-xl animate-float" style="animation-delay: 2.5s;">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase">Omset</p>
                        <p class="font-bold text-slate-800">Meningkat</p>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <section id="fitur" class="relative py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 mb-4">Fitur Lengkap Toko</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Dirancang khusus untuk memudahkan operasional harian toko Anda agar lebih efisien.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="group p-8 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-emerald-900/5 border border-slate-100 transition duration-300">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Kasir & Keuangan</h3>
                    <p class="text-slate-500 leading-relaxed">Catat transaksi penjualan dengan cepat, hitung kembalian otomatis, dan pantau arus kas masuk/keluar.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-teal-900/5 border border-slate-100 transition duration-300">
                    <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Manajemen Stok</h3>
                    <p class="text-slate-500 leading-relaxed">Pantau stok barang real-time. Dapatkan notifikasi otomatis saat barang hampir habis untuk segera restock.</p>
                </div>

                <div class="group p-8 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-blue-900/5 border border-slate-100 transition duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Laporan Lengkap</h3>
                    <p class="text-slate-500 leading-relaxed">Analisa perkembangan bisnis dengan grafik laporan penjualan harian, bulanan, hingga produk terlaris.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-8 text-center text-slate-400 text-sm bg-white border-t border-slate-100">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Built with ❤️ for UMKM.</p>
    </footer>

</body>
</html>
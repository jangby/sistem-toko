<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20"
         x-data="{ openModal: false, type: 'out' }">
        
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white p-6 rounded-b-[2.5rem] shadow-xl relative overflow-hidden z-10">
            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>

            <div class="flex items-center gap-3 mb-6 relative z-10">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold">Kas Operasional</h1>
                
                <form action="{{ route('kas.index') }}" method="GET" class="ml-auto">
                    <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()" 
                           class="bg-white/10 border-none text-xs rounded-lg text-white focus:ring-0 cursor-pointer">
                </form>
            </div>

            <div class="relative z-10 text-center mb-4">
                <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Saldo Kas Saat Ini</p>
                <h2 class="text-4xl font-black tracking-tight">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
            </div>

            <div class="grid grid-cols-2 gap-4 bg-white/10 p-4 rounded-2xl backdrop-blur-sm">
                <div>
                    <p class="text-[10px] text-gray-400">Total Masuk (Bln Ini)</p>
                    <p class="text-green-400 font-bold text-sm">+ Rp {{ number_format($totalMasukBulanIni + $totalPenjualanBulanIni, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-gray-500 italic">*Termasuk Penjualan</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-400">Total Keluar (Bln Ini)</p>
                    <p class="text-red-400 font-bold text-sm">- Rp {{ number_format($totalKeluarBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 -mt-6 relative z-20 flex gap-4">
            <button @click="openModal = true; type = 'in'" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-4 rounded-2xl shadow-lg shadow-green-500/30 flex flex-col items-center gap-1 transition transform active:scale-95">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </div>
                <span class="font-bold text-sm">Pemasukan</span>
            </button>
            <button @click="openModal = true; type = 'out'" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-4 rounded-2xl shadow-lg shadow-red-500/30 flex flex-col items-center gap-1 transition transform active:scale-95">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                </div>
                <span class="font-bold text-sm">Pengeluaran</span>
            </button>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mx-4 mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm text-center shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-4 mt-2">
            <h3 class="font-bold text-gray-800 dark:text-white mb-3 text-sm">Riwayat Operasional</h3>
            
            <div class="space-y-3">
                @forelse ($mutations as $item)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $item->type == 'in' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                @if($item->type == 'in')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white text-sm line-clamp-1">{{ $item->description }}</h4>
                                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-sm {{ $item->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item->type == 'in' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </span>
                            
                            <form action="{{ route('kas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')" class="inline-block mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] text-gray-300 hover:text-red-500">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="inline-block p-4 rounded-full bg-gray-100 mb-2">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-500 text-xs">Belum ada catatan operasional bulan ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div x-show="openModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">
            
            <div @click.away="openModal = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                        <span x-text="type === 'in' ? 'Tambah Pemasukan' : 'Catat Pengeluaran'"></span>
                    </h2>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('kas.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" x-model="type">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required 
                               class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" required placeholder="0" 
                               class="w-full text-lg font-bold rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan</label>
                        <textarea name="description" rows="2" required placeholder="Contoh: Bayar Listrik, Beli Kresek, Modal Tambahan..." 
                                  class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <button type="submit" 
                            :class="type === 'in' ? 'bg-green-500 hover:bg-green-600 shadow-green-500/30' : 'bg-red-500 hover:bg-red-600 shadow-red-500/30'"
                            class="w-full text-white py-3 rounded-xl font-bold shadow-lg transition">
                        Simpan Catatan
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
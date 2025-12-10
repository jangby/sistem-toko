<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20"
         x-data="{ openModal: false, type: 'deposit' }">
        
        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 text-white p-6 rounded-b-[2.5rem] shadow-xl relative overflow-hidden z-10">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>

            <div class="flex items-center gap-3 mb-6 relative z-10">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold">Dana Darurat</h1>
            </div>

            <div class="relative z-10 text-center mb-2">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-3 shadow-inner">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <p class="text-white/80 text-xs uppercase tracking-widest mb-1">Total Simpanan</p>
                <h2 class="text-4xl font-black tracking-tight">Rp {{ number_format($balance, 0, ',', '.') }}</h2>
            </div>
        </div>

        <div class="px-6 -mt-6 relative z-20 flex gap-4">
            <button @click="openModal = true; type = 'deposit'" class="flex-1 bg-white dark:bg-gray-800 py-4 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 flex flex-col items-center gap-1 hover:bg-gray-50 transition active:scale-95">
                <div class="bg-green-100 p-2 rounded-full text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="font-bold text-sm text-gray-700 dark:text-gray-300">Nabung</span>
            </button>
            <button @click="openModal = true; type = 'withdrawal'" class="flex-1 bg-white dark:bg-gray-800 py-4 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 flex flex-col items-center gap-1 hover:bg-gray-50 transition active:scale-95">
                <div class="bg-orange-100 p-2 rounded-full text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
                <span class="font-bold text-sm text-gray-700 dark:text-gray-300">Ambil</span>
            </button>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mx-4 mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm text-center">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mx-4 mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-4 mt-2">
            <h3 class="font-bold text-gray-800 dark:text-white mb-3 text-sm">Riwayat Transaksi</h3>
            <div class="space-y-3">
                @forelse ($history as $item)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $item->type == 'deposit' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                @if($item->type == 'deposit')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white text-sm">
                                    {{ $item->type == 'deposit' ? 'Setor Dana' : 'Tarik Dana' }}
                                </h4>
                                <p class="text-[10px] text-gray-400">
                                    {{ $item->description }} 
                                    @if($item->source == 'cash') <span class="text-red-400">(Potong Kas)</span> @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-sm {{ $item->type == 'deposit' ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $item->type == 'deposit' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($item->date)->format('d M') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-xs">Belum ada simpanan.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $history->links() }}</div>
        </div>

        <div x-show="openModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             style="display: none;">
            
            <div @click.away="openModal = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                        <span x-text="type === 'deposit' ? 'Tambah Simpanan' : 'Tarik Simpanan'"></span>
                    </h2>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('tabungan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" x-model="type">
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-gray-300 dark:bg-gray-700">
                    </div>

                    <div x-show="type === 'deposit'">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sumber Dana</label>
                        <select name="source" class="w-full rounded-xl border-gray-300 dark:bg-gray-700">
                            <option value="manual">Dari Luar (Dompet Pribadi)</option>
                            <option value="cash">Dari Kas Toko (Potong Otomatis)</option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 italic">*Jika pilih Kas Toko, saldo operasional akan berkurang.</p>
                    </div>

                    <input x-show="type === 'withdrawal'" type="hidden" name="source" value="manual">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" required class="w-full text-lg font-bold rounded-xl border-gray-300 dark:bg-gray-700">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                        <input type="text" name="description" placeholder="Contoh: Sisihkan Laba Minggu Ini" class="w-full rounded-xl border-gray-300 dark:bg-gray-700">
                    </div>

                    <button type="submit" 
                            :class="type === 'deposit' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-orange-500 hover:bg-orange-600'"
                            class="w-full text-white py-3 rounded-xl font-bold shadow-lg transition">
                        Proses
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
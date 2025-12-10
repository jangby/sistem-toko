<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20">
        
        <div class="bg-indigo-600 p-5 rounded-b-3xl shadow-lg mb-4">
            <div class="flex items-center gap-3 mb-4 text-white">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full bg-white/20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></a>
                <h1 class="text-xl font-bold">Manajemen Stok</h1>
            </div>
            
            @if($lowStocks->count() > 0)
                <div class="bg-red-500/20 border border-red-400/30 p-3 rounded-xl text-white backdrop-blur-sm">
                    <p class="text-xs font-bold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Perhatian: {{ $lowStocks->count() }} Barang Stok Kritis!
                    </p>
                </div>
            @else
                <div class="bg-green-500/20 border border-green-400/30 p-3 rounded-xl text-white backdrop-blur-sm text-xs font-bold">
                    Semua stok aman terkendali.
                </div>
            @endif
        </div>

        <div class="px-4 mb-6">
            <h3 class="font-bold text-gray-800 dark:text-white mb-2 text-sm">Harus Segera Dibeli</h3>
            <div class="flex gap-3 overflow-x-auto pb-2">
                @foreach($lowStocks as $item)
                    <div class="min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-xl shadow-sm border-l-4 border-red-500">
                        <h4 class="font-bold text-gray-800 dark:text-white text-xs truncate">{{ $item->name }}</h4>
                        <p class="text-[10px] text-gray-500">Sisa: <span class="text-red-600 font-bold text-lg">{{ $item->stock }}</span> {{ $item->unit }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-800 dark:text-white text-sm">Riwayat Pembelian</h3>
                <a href="{{ route('stok.create') }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-indigo-500/30">+ Buat Pesanan</a>
            </div>

            <div class="space-y-3">
    @foreach($purchases as $po)
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center group">
            
            <a href="{{ route('stok.show', $po->id) }}" class="flex-1">
                <div class="flex items-center gap-3">
                    <div>
                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $po->po_number }}</span>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mt-1">{{ $po->supplier->name }}</h4>
                        <p class="text-[10px] text-gray-400">
                            {{ \Carbon\Carbon::parse($po->date)->format('d M') }} • 
                            Rp {{ number_format($po->total_estimated) }}
                        </p>
                    </div>
                </div>
            </a>

            <div class="flex items-center gap-2">
                @if($po->status == 'completed')
                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">Selesai</span>
                @else
                    <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-1 rounded-full font-bold">Proses</span>
                @endif
                
                <a href="{{ route('stok.print', $po->id) }}" target="_blank" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-500 hover:text-red-500 hover:bg-red-50 transition" title="Cetak Surat Pesanan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </a>
            </div>

        </div>
    @endforeach
</div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20"
         x-data="{ tab: 'ringkasan' }"> <div class="bg-white dark:bg-gray-800 p-5 rounded-b-3xl shadow-sm z-10 sticky top-0">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Laporan Keuangan</h1>
            </div>

            <form action="{{ route('laporan.index') }}" method="GET" class="bg-gray-50 dark:bg-gray-700 p-3 rounded-xl border border-gray-100 dark:border-gray-600">
                <div class="flex items-center gap-2">
                    <div class="flex-1">
                        <label class="text-[10px] text-gray-400 block mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white py-1.5">
                    </div>
                    <span class="text-gray-400 mt-4">-</span>
                    <div class="flex-1">
                        <label class="text-[10px] text-gray-400 block mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white py-1.5">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </div>
            </form>

            <div class="flex mt-6 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                <button @click="tab = 'ringkasan'" 
                        :class="{ 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-white': tab === 'ringkasan', 'text-gray-500': tab !== 'ringkasan' }"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">
                    Ringkasan
                </button>
                <button @click="tab = 'riwayat'" 
                        :class="{ 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-white': tab === 'riwayat', 'text-gray-500': tab !== 'riwayat' }"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">
                    Riwayat
                </button>
                <button @click="tab = 'terlaris'" 
                        :class="{ 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-white': tab === 'terlaris', 'text-gray-500': tab !== 'terlaris' }"
                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">
                    Terlaris
                </button>
            </div>
        </div>

        <div class="p-4">
            
            <div x-show="tab === 'ringkasan'" x-transition.opacity>
                
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg mb-4 relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-blue-100 text-sm mb-1">Total Omset</p>
                        <h2 class="text-3xl font-black">Rp {{ number_format($totalOmset, 0, ',', '.') }}</h2>
                        <p class="text-xs text-blue-200 mt-2">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>
                    <div class="absolute -right-5 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-xs text-gray-500">Total Transaksi</p>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $totalTransaksi }} <span class="text-xs font-normal text-gray-400">Nota</span></h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <p class="text-xs text-gray-500">Barang Terjual</p>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $totalItemTerjual }} <span class="text-xs font-normal text-gray-400">Pcs</span></h3>
                    </div>
                </div>

                <a href="https://wa.me/?text={{ urlencode("Laporan Toko Periode $startDate s/d $endDate\n\n💰 Omset: Rp " . number_format($totalOmset) . "\n🧾 Transaksi: $totalTransaksi\n📦 Terjual: $totalItemTerjual Pcs\n\nDikirim dari Sistem Toko.") }}" 
                   target="_blank"
                   class="flex items-center justify-center gap-2 w-full bg-green-500 text-white py-3 rounded-xl font-bold hover:bg-green-600 shadow-lg shadow-green-500/20">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Kirim Laporan ke WA
                </a>
            </div>


            <div x-show="tab === 'riwayat'" x-transition.opacity style="display: none;">
                <div class="space-y-3">
                    @forelse ($history as $trx)
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white text-sm">{{ $trx->invoice_no }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $trx->created_at->format('d M H:i') }} • {{ $trx->cashier->name ?? 'Admin' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-blue-600 text-sm">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-gray-400 uppercase border border-gray-200 px-1.5 rounded">{{ $trx->payment_method }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-400">Tidak ada transaksi.</div>
                    @endforelse

                    <div class="mt-4">
                        {{ $history->withQueryString()->links() }}
                    </div>
                </div>
            </div>


            <div x-show="tab === 'terlaris'" x-transition.opacity style="display: none;">
                <div class="space-y-4">
                    @forelse ($topProducts as $index => $item)
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full {{ $index == 0 ? 'bg-yellow-400 text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center text-xs font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <h4 class="font-bold text-gray-800 dark:text-white text-sm">{{ $item->product->name }}</h4>
                                </div>
                                <span class="font-bold text-gray-800 dark:text-white text-sm">{{ $item->total_qty }} Pcs</span>
                            </div>
                            
                            <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-700">
                                @php
                                    // Hitung persentase terhadap item nomor 1 (agar barnya proporsional)
                                    $max = $topProducts->first()->total_qty;
                                    $percent = ($item->total_qty / $max) * 100;
                                @endphp
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-400">Belum ada data penjualan.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
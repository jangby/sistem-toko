<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20"
         x-data="{ openPay: false }">
        
        <div class="bg-white dark:bg-gray-800 p-4 sticky top-0 z-10 shadow-sm rounded-b-2xl">
            <div class="flex items-center gap-3">
                <a href="{{ route('debts.index', ['type' => $debt->type]) }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-lg font-bold text-gray-800 dark:text-white">Detail {{ $debt->type == 'receivable' ? 'Piutang' : 'Utang' }}</h1>
            </div>
        </div>

        <div class="p-5">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 text-center mb-6">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-2xl font-bold text-gray-500 mb-3">
                    {{ substr($debt->name, 0, 1) }}
                </div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $debt->name }}</h2>
                <p class="text-sm text-gray-400 mb-4">{{ $debt->phone ?? 'Tidak ada no HP' }}</p>

                @if($debt->phone && $debt->remaining > 0)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $debt->phone)) }}?text=Halo {{ $debt->name }}, sekadar mengingatkan tagihan sebesar Rp {{ number_format($debt->remaining, 0, ',', '.') }} di Toko Kami. Terima kasih." 
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full text-xs font-bold hover:bg-green-200 transition">
                       <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                       Ingatkan via WA
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-xl text-center">
                    <p class="text-xs text-gray-500 uppercase">Total Hutang</p>
                    <p class="font-bold text-gray-800 text-lg">Rp {{ number_format($debt->amount) }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-gray-800 p-4 rounded-xl text-center border border-blue-100">
                    <p class="text-xs text-blue-500 uppercase">Sisa Tagihan</p>
                    <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($debt->remaining) }}</p>
                </div>
            </div>

            <h3 class="font-bold text-gray-700 text-sm mb-3">Riwayat Pembayaran</h3>
            <div class="space-y-3 relative border-l-2 border-gray-200 ml-2 pl-4 pb-4">
                @forelse($debt->payments as $pay)
                    <div class="relative">
                        <div class="absolute -left-[25px] top-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pay->date)->translatedFormat('d F Y, H:i') }}</p>
                                <p class="font-bold text-sm text-gray-800">Bayar Cicilan</p>
                            </div>
                            <span class="font-bold text-green-600">+ Rp {{ number_format($pay->amount) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Belum ada riwayat pembayaran.</p>
                @endforelse
            </div>
        </div>

        @if($debt->remaining > 0)
            <div class="fixed bottom-0 w-full max-w-md bg-white border-t p-4 z-20">
                <button @click="openPay = true" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold shadow-lg">
                    + Tambah Pembayaran
                </button>
            </div>
        @endif

        <div x-show="openPay" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">
            
            <div @click.away="openPay = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Input Pembayaran Cicilan</h2>
                
                <form action="{{ route('debts.payment', $debt->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nominal Bayar (Rp)</label>
                        <input type="number" name="amount" max="{{ $debt->remaining }}" required placeholder="0" 
                               class="w-full text-lg font-bold rounded-xl border-gray-300 focus:ring-blue-500">
                        <p class="text-[10px] text-gray-400 mt-1">Maksimal: Rp {{ number_format($debt->remaining) }}</p>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">Simpan Pembayaran</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
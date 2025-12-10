<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20"
         x-data="{ openModal: false }">
        
        <div class="bg-white dark:bg-gray-800 p-4 sticky top-0 z-10 shadow-sm rounded-b-2xl">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Utang Piutang</h1>
            </div>

            <div class="flex p-1 bg-gray-100 dark:bg-gray-700 rounded-xl">
                <a href="{{ route('debts.index', ['type' => 'receivable']) }}" 
                   class="flex-1 text-center py-2 text-xs font-bold rounded-lg transition {{ $type == 'receivable' ? 'bg-white dark:bg-gray-600 shadow text-green-600 dark:text-white' : 'text-gray-500' }}">
                   Piutang (Kasbon)
                </a>
                <a href="{{ route('debts.index', ['type' => 'payable']) }}" 
                   class="flex-1 text-center py-2 text-xs font-bold rounded-lg transition {{ $type == 'payable' ? 'bg-white dark:bg-gray-600 shadow text-red-600 dark:text-white' : 'text-gray-500' }}">
                   Utang Toko
                </a>
            </div>
        </div>

        <div class="p-4 space-y-3">
            @forelse ($debts as $item)
                <a href="{{ route('debts.show', $item->id) }}" class="block bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 active:scale-95 transition relative overflow-hidden">
                    
                    @if($item->status == 'paid')
                        <div class="absolute top-0 right-0 bg-green-500 text-white text-[9px] px-2 py-0.5 rounded-bl-lg font-bold">LUNAS</div>
                    @else
                        <div class="absolute top-0 right-0 bg-gray-200 text-gray-500 text-[9px] px-2 py-0.5 rounded-bl-lg font-bold uppercase">{{ $item->status }}</div>
                    @endif

                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $item->description ?? 'Tanpa keterangan' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold {{ $type == 'receivable' ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </span>
                            @if($item->due_date)
                                <span class="text-[10px] text-red-400">Jatuh Tempo: {{ \Carbon\Carbon::parse($item->due_date)->format('d M') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-700 mt-2">
                        @php $percent = ($item->paid_amount / $item->amount) * 100; @endphp
                        <div class="{{ $type == 'receivable' ? 'bg-green-500' : 'bg-red-500' }} h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] text-gray-400">Terbayar: Rp {{ number_format($item->paid_amount) }}</span>
                        <span class="text-[10px] font-bold text-gray-600">Sisa: Rp {{ number_format($item->remaining) }}</span>
                    </div>
                </a>
            @empty
                <div class="text-center py-10 text-gray-400 text-sm">Belum ada data {{ $type == 'receivable' ? 'piutang' : 'utang' }}.</div>
            @endforelse
            
            <div class="mt-4">{{ $debts->withQueryString()->links() }}</div>
        </div>

        <button @click="openModal = true" 
                class="fixed bottom-6 right-6 lg:right-[calc(50%-12rem)] {{ $type == 'receivable' ? 'bg-green-600' : 'bg-red-600' }} text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition z-40">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>

        <div x-show="openModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">
            
            <div @click.away="openModal = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                        Catat {{ $type == 'receivable' ? 'Piutang Baru' : 'Utang Baru' }}
                    </h2>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('debts.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama {{ $type == 'receivable' ? 'Peminjam' : 'Pemberi Utang' }}</label>
                        <input type="text" name="name" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Nominal (Rp)</label>
                            <input type="number" name="amount" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Jatuh Tempo</label>
                            <input type="date" name="due_date" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">No. HP (Opsional untuk WA)</label>
                        <input type="number" name="phone" placeholder="08xxxxx" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Keterangan</label>
                        <textarea name="description" rows="2" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <button type="submit" class="w-full {{ $type == 'receivable' ? 'bg-green-600' : 'bg-red-600' }} text-white py-3 rounded-xl font-bold shadow-lg transition">Simpan</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
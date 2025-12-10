<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl pb-24">
        
        <div class="bg-indigo-600 p-5 rounded-b-3xl shadow-lg text-white mb-4">
            <h1 class="font-bold text-lg">Cek Barang Datang</h1>
            <div class="mt-2 flex justify-between text-xs opacity-90">
                <span>{{ $purchase->po_number }}</span>
                <span>{{ $purchase->supplier->name }}</span>
            </div>
            @if($purchase->status == 'completed')
                <div class="mt-3 bg-white/20 p-2 rounded-lg text-center text-sm font-bold">✅ Selesai Diproses</div>
            @endif
        </div>

        <form action="{{ route('stok.update', $purchase->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="px-4 space-y-3">
                @foreach($purchase->details as $index => $detail)
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative">
                        <input type="hidden" name="items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                        
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-gray-800 text-sm w-2/3">{{ $detail->product->name }}</h4>
                            <span class="text-[10px] bg-gray-100 px-2 py-1 rounded text-gray-500">
                                Pesan: {{ $detail->request_qty }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] text-gray-400 block mb-1">Jml Datang</label>
                                <input type="number" name="items[{{ $index }}][received_qty]" 
                                       value="{{ $purchase->status == 'completed' ? $detail->received_qty : $detail->request_qty }}" 
                                       class="w-full text-sm font-bold text-center border-gray-300 rounded-lg focus:ring-indigo-500 {{ $detail->received_qty < $detail->request_qty ? 'text-red-600' : 'text-green-600' }}"
                                       {{ $purchase->status == 'completed' ? 'disabled' : '' }}>
                            </div>

                            <div>
                                <label class="text-[10px] text-gray-400 block mb-1">Harga Beli Baru</label>
                                <input type="number" name="items[{{ $index }}][buy_price]" 
                                       value="{{ $detail->buy_price }}" 
                                       class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500"
                                       {{ $purchase->status == 'completed' ? 'disabled' : '' }}>
                            </div>
                        </div>

                        @if($purchase->status != 'completed')
                            <p class="text-[9px] text-red-400 mt-2 italic">*Jika barang kosong, isi Jml Datang dengan 0.</p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($purchase->status != 'completed')
                <div class="fixed bottom-0 w-full max-w-md bg-white border-t p-4 z-20">
                    <button type="submit" onclick="return confirm('Yakin data sudah benar? Stok akan ditambahkan ke gudang.')" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-green-500/30">
                        ✅ Selesai & Update Stok
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20" 
         x-data="supplierApp()">
        
        <div class="bg-white dark:bg-gray-800 p-4 sticky top-0 z-10 shadow-sm rounded-b-2xl">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Data Supplier</h1>
            </div>

            <form action="{{ route('suppliers.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau no hp..." 
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-orange-500 dark:text-white text-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mx-4 mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-4 space-y-3">
            @forelse ($suppliers as $supplier)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-3 group active:scale-95 transition-transform duration-100">
                    
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg">
                                {{ substr($supplier->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 dark:text-white">{{ $supplier->name }}</h3>
                                <p class="text-xs text-gray-400">ID: #{{ $supplier->id }}</p>
                            </div>
                        </div>
                        
                        <button @click="openEditModal({{ $supplier }})" 
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-full text-blue-600 hover:bg-blue-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-sm mt-1">
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $supplier->phone)) }}" target="_blank"
                           class="flex items-center gap-2 p-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="truncate">{{ $supplier->phone ?? '-' }}</span>
                        </a>

                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 text-gray-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">{{ $supplier->address ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-gray-500 text-sm">Belum ada data supplier.</p>
                </div>
            @endforelse

            <div class="mt-4 pb-20">
                {{ $suppliers->links() }}
            </div>
        </div>

        <button @click="openCreate = true" 
                class="fixed bottom-6 right-6 lg:right-[calc(50%-12rem)] bg-orange-500 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-orange-600 hover:scale-110 transition z-40">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>

        <div x-show="openCreate" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">
            
            <div @click.away="openCreate = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Supplier</h2>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama Supplier / Sales</label>
                        <input type="text" name="name" required placeholder="PT. Maju Jaya / Pak Budi" 
                               class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">No. Telepon / WA</label>
                        <input type="number" name="phone" placeholder="08xxxx" 
                               class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Alamat</label>
                        <textarea name="address" rows="2" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-orange-500"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:bg-orange-600">Simpan Supplier</button>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             style="display: none;">
            
            <div @click.away="openEdit = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Edit Supplier</h2>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form :action="updateUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama Supplier</label>
                        <input type="text" name="name" x-model="editData.name" required 
                               class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">No. Telepon</label>
                        <input type="text" name="phone" x-model="editData.phone" 
                               class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Alamat</label>
                        <textarea name="address" x-model="editData.address" rows="2" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Update</button>
                    </div>
                </form>

                <form :action="deleteUrl" method="POST" onsubmit="return confirm('Hapus supplier ini?')" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 text-red-600 py-3 rounded-xl font-bold hover:bg-red-200">Hapus Supplier</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function supplierApp() {
            return {
                openCreate: false,
                openEdit: false,
                editData: {},
                updateUrl: '',
                deleteUrl: '',

                openEditModal(supplier) {
                    this.editData = supplier;
                    this.updateUrl = "{{ route('suppliers.index') }}/" + supplier.id;
                    this.deleteUrl = "{{ route('suppliers.index') }}/" + supplier.id;
                    this.openEdit = true;
                }
            }
        }
    </script>
</x-app-layout>
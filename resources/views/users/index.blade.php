<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20" 
         x-data="userApp()">
        
        <div class="bg-white dark:bg-gray-800 p-4 sticky top-0 z-10 shadow-sm rounded-b-2xl">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Kelola Akun</h1>
            </div>

            <form action="{{ route('users.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." 
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-purple-500 dark:text-white text-sm">
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
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                 class="mx-4 mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-4 space-y-3">
            @forelse ($users as $user)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between group">
                    
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg {{ $user->role == 'admin' ? 'bg-blue-500 shadow-blue-500/30' : 'bg-green-500 shadow-green-500/30' }} shadow-lg">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-800 dark:text-white">{{ $user->name }}</h3>
                                @if($user->id == Auth::id())
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 rounded border border-gray-200">(Anda)</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full mt-1 inline-block {{ $user->role == 'admin' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>

                    <button @click="openEditModal({{ $user }})" class="p-2 text-gray-400 hover:text-blue-600 bg-gray-50 dark:bg-gray-700 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>

                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-gray-500 text-sm">Belum ada pengguna lain.</p>
                </div>
            @endforelse

            <div class="mt-4 pb-20">
                {{ $users->links() }}
            </div>
        </div>

        <button @click="openCreate = true" 
                class="fixed bottom-6 right-6 lg:right-[calc(50%-12rem)] bg-purple-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-purple-700 hover:scale-110 transition z-40">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        </button>

        <div x-show="openCreate" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">
            
            <div @click.away="openCreate = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Pengguna</h2>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-3">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Email (Untuk Login)</label>
                        <input type="email" name="email" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Role / Jabatan</label>
                        <select name="role" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-purple-500">
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin (Owner)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Password</label>
                        <input type="password" name="password" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-purple-500">
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:bg-purple-700 mt-2">Buat Akun</button>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
             x-transition:enter="transition ease-out duration-300"
             style="display: none;">
            
            <div @click.away="openEdit = false" class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Edit Pengguna</h2>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form :action="updateUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Email</label>
                        <input type="email" name="email" x-model="editData.email" required class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500">Role</label>
                        <select name="role" x-model="editData.role" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin (Owner)</option>
                        </select>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <p class="text-[10px] text-gray-400 mb-2">Kosongkan jika tidak ingin mengganti password</p>
                        <div class="space-y-2">
                            <input type="password" name="password" placeholder="Password Baru" class="w-full rounded-lg border-gray-300 text-sm">
                            <input type="password" name="password_confirmation" placeholder="Ulangi Password" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                    </div>

                    <div class="flex gap-2 mt-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Update</button>
                    </div>
                </form>

                <form x-show="editData.id != {{ Auth::id() }}" :action="deleteUrl" method="POST" onsubmit="return confirm('Yakin hapus akun ini?')" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-100 text-red-600 py-3 rounded-xl font-bold hover:bg-red-200">Hapus Akun</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function userApp() {
            return {
                openCreate: false,
                openEdit: false,
                editData: {},
                updateUrl: '',
                deleteUrl: '',

                openEditModal(user) {
                    this.editData = user;
                    this.updateUrl = "{{ route('users.index') }}/" + user.id;
                    this.deleteUrl = "{{ route('users.index') }}/" + user.id;
                    this.openEdit = true;
                }
            }
        }
    </script>
</x-app-layout>
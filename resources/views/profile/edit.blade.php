<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20"
         x-data="{ showToast: false }"
         x-init="@if (session('status') === 'profile-updated' || session('status') === 'password-updated') showToast = true; setTimeout(() => showToast = false, 3000) @endif">
        
        <div class="bg-blue-600 dark:bg-gray-800 pb-16 pt-6 px-6 rounded-b-[3rem] shadow-lg relative">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-white">Pengaturan Akun</h1>
            </div>

            <div class="flex flex-col items-center">
                <div class="w-24 h-24 rounded-full bg-white p-1 shadow-xl mb-3">
                    <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center text-3xl font-bold text-gray-500 overflow-hidden">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <h2 class="text-xl font-bold text-white">{{ Auth::user()->name }}</h2>
                <span class="text-blue-200 text-sm bg-blue-700/50 px-3 py-1 rounded-full mt-1 border border-blue-500/30">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
        </div>

        <div class="px-4 -mt-10 space-y-6">

            <div x-show="showToast" 
                 x-transition.opacity.duration.500ms
                 class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-full shadow-xl z-50 text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Data berhasil disimpan!
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Pribadi
                </h3>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                               class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 text-sm">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                               class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 text-sm">
                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Ganti Password
                </h3>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="••••••••" 
                               class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-orange-500 text-sm">
                        @error('current_password', 'updatePassword') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Password Baru</label>
                        <input type="password" name="password" placeholder="••••••••" 
                               class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-orange-500 text-sm">
                        @error('password', 'updatePassword') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••" 
                               class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-orange-500 text-sm">
                    </div>

                    <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-lg shadow-orange-500/20">
                        Update Password
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" onclick="return confirm('Yakin ingin keluar?')"
                        class="w-full bg-red-50 text-red-600 border border-red-200 py-3.5 rounded-xl font-bold text-sm hover:bg-red-100 transition flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar Aplikasi
                </button>
            </form>

            <div class="text-center pb-8">
                <p class="text-[10px] text-gray-400">Versi Aplikasi v1.0.0</p>
            </div>

        </div>
    </div>
</x-app-layout>
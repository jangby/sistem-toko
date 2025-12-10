<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 dark:bg-gray-900 shadow-2xl relative pb-20">
        
        <div class="bg-teal-600 p-6 rounded-b-[2.5rem] shadow-lg mb-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            
            <div class="flex items-center gap-3 mb-2 relative z-10">
                <a href="{{ route('dashboard') }}" class="p-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm transition text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold text-white">Integrasi WhatsApp</h1>
            </div>
            <p class="text-teal-100 text-xs ml-11">Konfigurasi Server WAHA (API)</p>
        </div>

        <div class="px-4">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                     class="mb-4 p-3 bg-green-100 text-green-700 rounded-xl text-sm text-center flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('settings.wa.update') }}" method="POST" class="space-y-5">
    @csrf

    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2 text-sm uppercase tracking-wide">
            <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
            Server WAHA
        </h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">WAHA API URL</label>
                <input type="text" name="waha_url" value="{{ old('waha_url', $settings['waha_url'] ?? '') }}" 
                       class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-teal-500 text-sm" 
                       placeholder="http://localhost:3000">
                <p class="text-[10px] text-gray-400 mt-1">Wajib menggunakan <b>http://</b> atau <b>https://</b></p>
                @error('waha_url') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">API Key (Secret)</label>
                <input type="text" name="waha_api_key" value="{{ old('waha_api_key', $settings['waha_api_key'] ?? '') }}" 
                       class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-teal-500 text-sm">
                @error('waha_api_key') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Session Name</label>
                <input type="text" name="waha_session" value="{{ old('waha_session', $settings['waha_session'] ?? 'default') }}" 
                       class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-teal-500 text-sm">
                @error('waha_session') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2 text-sm uppercase tracking-wide">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Tujuan Notifikasi
        </h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Nomor WA Admin (Owner)</label>
                <input type="number" name="admin_wa" value="{{ old('admin_wa', $settings['admin_wa'] ?? '') }}" 
                       class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-green-500 text-sm" placeholder="62812xxxx">
                @error('admin_wa') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">ID Grup Toko (Laporan Harian)</label>
                <input type="text" name="waha_group_id" value="{{ old('waha_group_id', $settings['waha_group_id'] ?? '') }}" 
                       class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-green-500 text-sm" placeholder="Contoh: xxxx@g.us">
                <p class="text-[10px] text-gray-400 mt-1">
                    Tips: Kirim pesan <code>!id</code> ke bot di grup untuk tahu ID-nya.
                </p>
                @error('waha_group_id') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mt-5">
    <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2 text-sm uppercase tracking-wide">
        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        Preferensi Pesan
    </h3>

    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">Kirim Info Tiap Transaksi?</label>
        <select name="wa_notification_trx" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-purple-500 text-sm">
            <option value="1" {{ ($settings['wa_notification_trx'] ?? '0') == '1' ? 'selected' : '' }}>✅ YA - Kirim Rincian ke Grup</option>
            <option value="0" {{ ($settings['wa_notification_trx'] ?? '0') == '0' ? 'selected' : '' }}>❌ TIDAK - Jangan Kirim</option>
        </select>
        <p class="text-[10px] text-gray-400 mt-1">
            Jika "YA", setiap kali kasir mencetak struk, rincian barang akan dikirim ke Grup WA Toko.
        </p>
    </div>
</div>

    <button type="submit" class="w-full bg-teal-600 text-white py-4 rounded-xl font-bold shadow-lg shadow-teal-500/30 hover:bg-teal-700 transition transform active:scale-95">
        Simpan Pengaturan
    </button>
</form>
        </div>
    </div>
</x-app-layout>
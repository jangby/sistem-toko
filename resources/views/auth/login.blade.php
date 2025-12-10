<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50 dark:bg-gray-900">
    
    <div class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-b-[3rem] z-0 shadow-lg"></div>
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl z-0"></div>
        <div class="absolute top-20 right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl z-0"></div>

        <div class="relative z-10 w-full max-w-sm px-6">
            
            <div class="text-center mb-8">
                <div class="mx-auto w-20 h-20 bg-white rounded-2xl shadow-lg flex items-center justify-center mb-4 transform rotate-3 hover:rotate-0 transition duration-300">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">POS SYSTEM</h2>
                <p class="text-blue-200 text-sm">Masuk untuk mengelola toko</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-2xl">
                
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                   class="pl-10 w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 transition py-3 text-sm font-medium placeholder-gray-300" 
                                   placeholder="nama@email.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   class="pl-10 w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 transition py-3 text-sm font-medium placeholder-gray-300"
                                   placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">Ingat Saya</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a class="text-xs text-blue-600 hover:text-blue-800 font-bold" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.02] transition transform duration-200">
                        Masuk Aplikasi
                    </button>
                </form>
            </div>

            <div class="text-center mt-8">
                <p class="text-xs text-gray-400">© {{ date('Y') }} Sistem Toko. All rights reserved.</p>
            </div>

        </div>
    </div>
</body>
</html>
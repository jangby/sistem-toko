<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
// use App\Http\Controllers\CategoryController; // (Nanti dibuat)
// use App\Http\Controllers\SupplierController; // (Nanti dibuat)
// use App\Http\Controllers\TransactionController; // (Nanti dibuat)
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN DEPAN (PUBLIC) ---
Route::get('/', function () {
    // Jika sudah login, langsung lempar ke dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('/welcome'); // Langsung buka form login
});



// --- 2. HALAMAN SETELAH LOGIN (UMUM) ---
// Semua yang ada di dalam group ini HARUS login dulu
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard: Tampilannya beda tergantung role (Optional logic di view)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    // Profile User (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- 3. KHUSUS ADMIN (MANAJEMEN DATA) ---
// Hanya user dengan role 'admin' yang bisa masuk sini
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // Route Resource otomatis membuat jalur untuk:
    // index (list), create (form), store (simpan), edit, update, destroy
    
    // Manajemen Barang
    Route::resource('products', ProductController::class);

    // Manajemen Kategori (Aktifkan nanti setelah Controller dibuat)
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);

    Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
    
    // Manajemen Supplier (Aktifkan nanti setelah Controller dibuat)
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    Route::get('/transaksi', [App\Http\Controllers\TransactionController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/store', [App\Http\Controllers\TransactionController::class, 'store'])->name('transaksi.store');

Route::resource('users', \App\Http\Controllers\UserController::class);
// Manajemen Utang Piutang
Route::post('/debts/{id}/payment', [App\Http\Controllers\DebtController::class, 'addPayment'])->name('debts.payment');
Route::resource('debts', \App\Http\Controllers\DebtController::class);
Route::resource('tabungan', \App\Http\Controllers\SavingController::class)->only(['index', 'store']);
});

// --- 4. KHUSUS KASIR (TRANSAKSI) ---
// User dengan role 'kasir' masuk sini. 
// Jika Admin juga boleh jualan, middlewarenya bisa diatur logic-nya.
Route::middleware(['auth', 'role:kasir'])->group(function () {
    
    // Halaman Kasir / Point of Sales
    Route::get('/transaksi', [App\Http\Controllers\TransactionController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/store', [App\Http\Controllers\TransactionController::class, 'store'])->name('transaksi.store');

Route::resource('kas', \App\Http\Controllers\CashController::class)->only(['index', 'store', 'destroy']);

// Manajemen Stok / Kulakan
Route::get('/stok/{id}/print', [App\Http\Controllers\StockController::class, 'print'])->name('stok.print');

Route::resource('stok', \App\Http\Controllers\StockController::class);

Route::resource('users', \App\Http\Controllers\UserController::class);


});


// --- FILE AUTHENTIKASI BAWAAN BREEZE ---
require __DIR__.'/auth.php';
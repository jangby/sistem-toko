<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // 1. Tabel Header Transaksi
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_no')->unique(); // INV-20251210-001
        $table->foreignId('user_id')->constrained(); // Siapa kasirnya
        $table->decimal('total_amount', 15, 2); // Total Belanja
        $table->decimal('pay_amount', 15, 2); // Uang dibayar
        $table->decimal('change_amount', 15, 2); // Kembalian
        $table->enum('payment_method', ['cash', 'qris'])->default('cash');
        $table->timestamps();
    });

    // 2. Tabel Detail (Barang apa saja yang dibeli)
    Schema::create('transaction_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_id')->constrained(); // Jangan cascade delete agar data history aman
        $table->integer('qty');
        $table->decimal('price', 15, 2); // Harga saat transaksi (penting jika harga barang berubah nanti)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

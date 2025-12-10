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
    Schema::create('debts', function (Blueprint $table) {
        $table->id();
        $table->enum('type', ['receivable', 'payable']); // receivable=Piutang (Kasbon), payable=Utang Kita
        $table->string('name'); // Nama Peminjam / Pemberi Utang
        $table->string('phone')->nullable(); // No WA untuk nagih
        $table->decimal('amount', 15, 2); // Total Hutang Awal
        $table->decimal('paid_amount', 15, 2)->default(0); // Total yang sudah dibayar
        $table->date('due_date')->nullable(); // Jatuh Tempo
        $table->text('description')->nullable(); // Catatan (misal: "Ambil Rokok 2 slop")
        $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};

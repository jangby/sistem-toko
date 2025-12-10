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
    Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->string('po_number')->unique(); // PO-20251210-001
        $table->foreignId('supplier_id')->constrained();
        $table->date('date');
        $table->enum('status', ['pending', 'completed'])->default('pending'); // Pending = Pesan, Completed = Sudah Masuk Stok
        $table->decimal('total_estimated', 15, 2)->default(0); // Estimasi biaya
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

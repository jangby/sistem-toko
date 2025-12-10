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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        // Relasi ke kategori dan supplier
        $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
        
        $table->string('name');
        $table->string('barcode')->unique()->nullable(); // Untuk scan
        $table->integer('stock')->default(0);
        $table->integer('min_stock')->default(5); // Batas minimal untuk trigger WA
        $table->string('unit')->default('pcs'); // pcs, kg, pack
        
        $table->decimal('buy_price', 15, 2)->default(0); // Harga Modal
        $table->decimal('sell_price', 15, 2)->default(0); // Harga Jual
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

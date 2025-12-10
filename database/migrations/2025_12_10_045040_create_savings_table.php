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
    Schema::create('savings', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->enum('type', ['deposit', 'withdrawal']); // Nabung / Ambil
        $table->decimal('amount', 15, 2);
        $table->enum('source', ['manual', 'cash'])->default('manual'); // Manual=Luar, Cash=Kas Toko
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};

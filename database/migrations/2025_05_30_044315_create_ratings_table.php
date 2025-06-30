<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('rating_id');
            $table->foreignId('pembeli_id')->constrained('pembeli', 'pembeli_id')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang', 'barang_id')->onDelete('cascade');
            $table->foreignId('transaksi_id')->constrained('transaksi', 'transaksi_id')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('Rating 1-5 stars');
            $table->text('review')->nullable()->comment('Optional review text');
            $table->timestamps();
            
            // Ensure one rating per buyer per item
            $table->unique(['pembeli_id', 'barang_id'], 'unique_buyer_item_rating');
            
            // Add indexes for performance
            $table->index(['barang_id', 'rating']);
            $table->index(['pembeli_id']);
            $table->index(['transaksi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->foreignId('barang_id');
            $table->foreignId('transaksi_id');
            $table->float('subtotal')->nullable();

            // Composite primary key (optional, if no id)
            $table->primary(['barang_id', 'transaksi_id']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('diskusi_produk', function (Blueprint $table) {
            $table->id('diskusi_id');
            $table->foreignId('pembeli_id')->nullable();
            $table->foreignId('barang_id')->nullable();
            $table->text('pertanyaan')->nullable();
            $table->text('jawaban')->nullable();
            $table->dateTime('tanggal_diskusi')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi_produk');
    }
};

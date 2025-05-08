<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->foreignId('pembeli_id')->nullable();
            $table->foreignId('cs_id')->nullable();
            $table->dateTime('tanggal_pelunasan')->nullable();
            $table->integer('point_digunakan')->default(0);
            $table->integer('point_diperoleh')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('metode_pengiriman')->default('diantar');
            $table->dateTime('tanggal_pesan')->nullable();
            $table->float('total_harga')->nullable();
            $table->string('status_transaksi')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};

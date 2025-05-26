<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id('pengiriman_id');
            $table->foreignId('pengirim_id')->nullable();
            $table->foreignId('transaksi_id')->nullable();
            $table->foreignId('alamat_id')->nullable();
            $table->string('status_pengiriman')->nullable();
            $table->dateTime('tanggal_kirim')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->dateTime('tanggal_terima')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};

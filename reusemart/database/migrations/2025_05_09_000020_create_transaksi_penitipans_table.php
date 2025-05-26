<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_penitipan', function (Blueprint $table) {
            $table->id('transaksi_penitipan_id');
            $table->foreignId('penitip_id')->nullable();
            $table->foreignId('barang_id')->nullable();
            $table->dateTime('batas_penitipan')->nullable();
            $table->dateTime('tanggal_penitipan')->nullable();
            $table->string('metode_penitipan')->default('diantar');
            $table->string('status_perpanjangan')->nullable();
            $table->string('status_penitipan', 50)->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_penitipan');
    }
};

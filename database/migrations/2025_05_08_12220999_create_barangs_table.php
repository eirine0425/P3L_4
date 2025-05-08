<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('barang_id');
            $table->foreignId('penitip_id')->nullable();
            $table->foreignId('kategori_id')->nullable();
            $table->char('status', 50)->default('belum_terjual');
            $table->char('kondisi', 50)->default('baru');
            $table->string('nama_barang')->nullable();
            $table->float('harga')->nullable();
            $table->float('rating')->nullable();
            $table->string('deskripsi')->nullable();
            $table->date('tanggal_penitipan')->nullable();
            $table->foreignId('garansi_id')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

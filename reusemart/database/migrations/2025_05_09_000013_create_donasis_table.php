<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donasi', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('barang_id')->nullable();
            $table->string('deskripsi')->nullable();
            $table->char('nama_kategori', 255)->nullable();
            $table->string('nama_penerima')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};

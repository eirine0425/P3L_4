<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->id('kategori_id');
            $table->char('nama_kategori', 255)->nullable();
            $table->string('deskripsi')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_barang');
    }
};

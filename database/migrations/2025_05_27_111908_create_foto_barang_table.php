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
        Schema::create('foto_barang', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('barang_id');
    $table->string('path');
    $table->timestamps();

    $table->foreign('barang_id')->references('barang_id')->on('barang')->onDelete('cascade');
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_barang');
    }
};

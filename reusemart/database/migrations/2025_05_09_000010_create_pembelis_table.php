<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembeli', function (Blueprint $table) {
            $table->id('pembeli_id');
            $table->char('nama', 255)->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('keranjang_id')->nullable();
            $table->integer('poin_loyalitas')->nullable();
            $table->dateTime('tanggal_registrasi')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembeli');
    }
};

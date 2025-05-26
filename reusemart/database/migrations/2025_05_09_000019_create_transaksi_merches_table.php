<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_merch', function (Blueprint $table) {
            $table->foreignId('pembeli_id');
            $table->foreignId('merch_id');
            $table->dateTime('tanggal_penukaran')->nullable();
            $table->string('status')->nullable();

            // Menjadikan kombinasi sebagai primary key
            $table->primary(['pembeli_id', 'merch_id']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_merch');
    }
};

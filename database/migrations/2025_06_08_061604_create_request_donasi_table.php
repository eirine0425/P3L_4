<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('request_donasi', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('organisasi_id')->nullable();
            $table->date('tanggal_request')->nullable();
            $table->string('status_request')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('jumlah_barang_diminta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('request_donasi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestDonasiTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_donasi', function (Blueprint $table) {
            $table->id('request_id'); // Primary key
            $table->unsignedBigInteger('organisasi_id');
            $table->text('deskripsi');
            $table->date('tanggal_request');
            $table->string('status_request', 50); // Bisa disesuaikan panjangnya

            // Jika perlu foreign key ke tabel organisasi, tambahkan baris di bawah ini:
            // $table->foreign('organisasi_id')->references('id')->on('organisasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_donasi');
    }
}

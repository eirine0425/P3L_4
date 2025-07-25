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
        Schema::table('transaksi_penitipan', function (Blueprint $table) {
        $table->boolean('status_perpanjangan')->default(false);
        $table->date('masa_akhir_penitipan')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_penitipan', function (Blueprint $table) {
            //
        });
    }
};

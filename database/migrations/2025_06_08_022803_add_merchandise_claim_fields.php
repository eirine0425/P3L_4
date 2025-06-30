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
        Schema::table('transaksi_merch', function (Blueprint $table) {
            // Add tanggal_ambil field
            $table->date('tanggal_ambil')->nullable()->after('status');
            
            // Add catatan field
            $table->text('catatan')->nullable()->after('tanggal_ambil');
            
            // Update status field to include new statuses
            $table->string('status', 50)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_merch', function (Blueprint $table) {
            $table->dropColumn(['tanggal_ambil', 'catatan']);
        });
    }
};

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
        Schema::table('transaksi', function (Blueprint $table) {
            // Add alamat_id column if it doesn't exist
            if (!Schema::hasColumn('transaksi', 'alamat_id')) {
                $table->foreignId('alamat_id')->nullable()->after('cs_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Remove the column if it exists
            if (Schema::hasColumn('transaksi', 'alamat_id')) {
                $table->dropColumn('alamat_id');
            }
        });
    }
};

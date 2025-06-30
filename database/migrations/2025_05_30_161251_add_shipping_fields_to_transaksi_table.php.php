<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('alamat_id')->nullable()->after('cs_id');
            $table->decimal('subtotal', 15, 2)->default(0)->after('metode_pengiriman');
            $table->decimal('ongkos_kirim', 15, 2)->default(0)->after('subtotal');
            $table->decimal('point_discount', 15, 2)->default(0)->after('ongkos_kirim');
            $table->string('keterangan_ongkir')->nullable()->after('point_discount');
            
            // Update existing columns
            $table->decimal('total_harga', 15, 2)->change();
            
            // Add foreign key constraint
            $table->foreign('alamat_id')->references('alamat_id')->on('alamats')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['alamat_id']);
            $table->dropColumn([
                'alamat_id',
                'subtotal',
                'ongkos_kirim', 
                'point_discount',
                'keterangan_ongkir'
            ]);
        });
    }
};

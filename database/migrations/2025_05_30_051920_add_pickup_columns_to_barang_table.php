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
        Schema::table('barang', function (Blueprint $table) {
            // Update status enum to include 'diambil_kembali'
            $table->enum('status', [
                'belum_terjual', 
                'terjual', 
                'sold_out', 
                'untuk_donasi', 
                'diambil_kembali'
            ])->default('belum_terjual')->change();
            
            // Add pickup related columns
            $table->timestamp('tanggal_pengambilan')->nullable()->after('tanggal_batas_penitipan');
            $table->text('catatan_pengambilan')->nullable()->after('tanggal_pengambilan');
            $table->enum('metode_pengambilan', [
                'penitip_pickup',      // Diambil langsung oleh penitip
                'courier_delivery',    // Dikirim via kurir
                'warehouse_storage'    // Disimpan di gudang
            ])->nullable()->after('catatan_pengambilan');
            
            // Add pickup confirmation details
            $table->unsignedBigInteger('pegawai_pickup_id')->nullable()->after('metode_pengambilan');
            $table->string('nomor_resi_pickup')->nullable()->after('pegawai_pickup_id');
            $table->decimal('biaya_pengambilan', 10, 2)->default(0)->after('nomor_resi_pickup');
            $table->enum('status_pengambilan', [
                'menunggu_konfirmasi',
                'siap_diambil',
                'dalam_pengiriman',
                'selesai'
            ])->nullable()->after('biaya_pengambilan');
            
            // Add foreign key constraint
            $table->foreign('pegawai_pickup_id')->references('id')->on('pegawai')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index(['status', 'tanggal_batas_penitipan']);
            $table->index(['status_pengambilan']);
            $table->index(['tanggal_pengambilan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('barang', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['pegawai_pickup_id']);
            
            // Drop indexes
            $table->dropIndex(['status', 'tanggal_batas_penitipan']);
            $table->dropIndex(['status_pengambilan']);
            $table->dropIndex(['tanggal_pengambilan']);
            
            // Drop columns
            $table->dropColumn([
                'tanggal_pengambilan',
                'catatan_pengambilan',
                'metode_pengambilan',
                'pegawai_pickup_id',
                'nomor_resi_pickup',
                'biaya_pengambilan',
                'status_pengambilan'
            ]);
            
            // Revert status enum to original values
            $table->enum('status', [
                'belum_terjual', 
                'terjual', 
                'sold_out', 
                'untuk_donasi'
            ])->default('belum_terjual')->change();
        }); 
    }
};

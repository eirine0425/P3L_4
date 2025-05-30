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
            // Add pickup-related columns
            $table->unsignedBigInteger('pickup_schedule_id')->nullable()->after('foto_barang');
            $table->timestamp('pickup_requested_at')->nullable()->after('pickup_schedule_id');
            $table->enum('status_pengambilan', ['belum_dijadwalkan', 'dijadwalkan', 'sedang_diproses', 'selesai', 'dibatalkan'])->default('belum_dijadwalkan')->after('pickup_requested_at');
            $table->date('tanggal_pengambilan')->nullable()->after('status_pengambilan');
            $table->text('catatan_pengambilan')->nullable()->after('tanggal_pengambilan');
            $table->enum('metode_pengambilan', ['ambil_sendiri', 'kirim_kurir'])->nullable()->after('catatan_pengambilan');
            $table->unsignedBigInteger('pegawai_pickup_id')->nullable()->after('metode_pengambilan');
            $table->string('nomor_resi_pickup')->nullable()->after('pegawai_pickup_id');
            $table->decimal('biaya_pengambilan', 10, 2)->nullable()->after('nomor_resi_pickup');
            
            // Add indexes for better performance
            $table->index(['pickup_schedule_id']);
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
            // Drop indexes first
            $table->dropIndex(['pickup_schedule_id']);
            $table->dropIndex(['status_pengambilan']);
            $table->dropIndex(['tanggal_pengambilan']);
            
            // Drop columns
            $table->dropColumn([
                'pickup_schedule_id',
                'pickup_requested_at',
                'status_pengambilan',
                'tanggal_pengambilan',
                'catatan_pengambilan',
                'metode_pengambilan',
                'pegawai_pickup_id',
                'nomor_resi_pickup',
                'biaya_pengambilan'
            ]);
        });
    }
};

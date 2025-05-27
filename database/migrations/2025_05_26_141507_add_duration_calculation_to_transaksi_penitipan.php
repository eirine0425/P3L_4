<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Update existing records to calculate batas_penitipan if not set
        DB::statement("
            UPDATE transaksi_penitipan 
            SET batas_penitipan = DATE_ADD(tanggal_penitipan, INTERVAL 30 DAY)
            WHERE batas_penitipan IS NULL 
            AND tanggal_penitipan IS NOT NULL
        ");
        
        // Set tanggal_penitipan to current date for records that don't have it
        DB::statement("
            UPDATE transaksi_penitipan 
            SET tanggal_penitipan = NOW(),
                batas_penitipan = DATE_ADD(NOW(), INTERVAL 30 DAY)
            WHERE tanggal_penitipan IS NULL
        ");
    }

    public function down(): void
    {
        // This migration doesn't create new columns, so no rollback needed
        // The automatic calculation will be removed when the model changes are reverted
    }
};

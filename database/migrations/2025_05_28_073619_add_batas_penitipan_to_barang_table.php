<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddBatasPenitipanToBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->date('batas_penitipan')->nullable()->after('tanggal_penitipan');
        });
        
        // Update existing records to set batas_penitipan = tanggal_penitipan + 30 days
        DB::statement('UPDATE barang SET batas_penitipan = DATE_ADD(tanggal_penitipan, INTERVAL 30 DAY) WHERE tanggal_penitipan IS NOT NULL');
        DB::statement('UPDATE barang SET batas_penitipan = DATE_ADD(created_at, INTERVAL 30 DAY) WHERE tanggal_penitipan IS NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('batas_penitipan');
        });
    }
}

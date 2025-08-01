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
        $table->dateTime('batas_penitipan')->nullable()->after('tanggal_penitipan');
    });
}

public function down(): void
{
    Schema::table('barang', function (Blueprint $table) {
        $table->dropColumn('batas_penitipan');
    });
}

};

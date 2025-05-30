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
        Schema::table('pickup_schedules', function (Blueprint $table) {
    $table->unsignedBigInteger('penitip_id')->after('id'); // atau kolom lain yg cocok
    $table->foreign('penitip_id')->references('penitip_id')->on('penitip')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_schedules', function (Blueprint $table) {
            //
        });
    }
};

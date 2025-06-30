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
        Schema::create('pickup_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penitip_id');
            $table->json('barang_ids'); // Array of barang IDs to be picked up
            $table->datetime('scheduled_date');
            $table->enum('pickup_method', [
                'penitip_pickup',
                'courier_delivery',
                'warehouse_storage'
            ]);
            $table->text('pickup_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('scheduled');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by'); // Pegawai who created the schedule
            $table->unsignedBigInteger('handled_by')->nullable(); // Pegawai who handles the pickup
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('penitip_id')->references('id')->on('penitip')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('pegawai')->onDelete('cascade');
            $table->foreign('handled_by')->references('id')->on('pegawai')->onDelete('set null');
            
            // Indexes
            $table->index(['penitip_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
            $table->index(['created_by']);
            $table->index(['handled_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_schedules');
    }
};

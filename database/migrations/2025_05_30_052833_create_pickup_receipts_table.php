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
        Schema::create('pickup_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('penitip_id');
            $table->json('barang_ids'); // Array of picked up barang IDs
            $table->datetime('pickup_date');
            $table->enum('pickup_method', [
                'penitip_pickup',
                'courier_delivery',
                'warehouse_storage'
            ]);
            $table->decimal('total_commission_earned', 12, 2)->default(0);
            $table->decimal('pickup_fee', 10, 2)->default(0);
            $table->decimal('storage_fee', 10, 2)->default(0);
            $table->text('pickup_address')->nullable();
            $table->string('received_by')->nullable(); // Name of person who received items
            $table->string('delivered_by')->nullable(); // Name of courier/staff
            $table->text('condition_notes')->nullable(); // Condition of items when picked up
            $table->json('photos')->nullable(); // Photos of items during pickup
            $table->unsignedBigInteger('processed_by'); // Pegawai who processed the pickup
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('penitip_id')->references('id')->on('penitip')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('pegawai')->onDelete('cascade');
            
            // Indexes
            $table->index(['penitip_id', 'pickup_date']);
            $table->index(['receipt_number']);
            $table->index(['pickup_date']);
            $table->index(['processed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_receipts');
    }
};

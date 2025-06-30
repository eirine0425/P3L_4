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
        Schema::table('penitip', function (Blueprint $table) {
            // Add notification preferences for pickup
            $table->boolean('notify_pickup_ready')->default(true)->after('alamat');
            $table->boolean('notify_pickup_reminder')->default(true)->after('notify_pickup_ready');
            $table->enum('preferred_pickup_method', [
                'penitip_pickup',
                'courier_delivery',
                'warehouse_storage'
            ])->default('penitip_pickup')->after('notify_pickup_reminder');
            $table->text('pickup_notes')->nullable()->after('preferred_pickup_method');
            
            // Add pickup statistics
            $table->integer('total_items_picked_up')->default(0)->after('pickup_notes');
            $table->timestamp('last_pickup_date')->nullable()->after('total_items_picked_up');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penitip', function (Blueprint $table) {
            $table->dropColumn([
                'notify_pickup_ready',
                'notify_pickup_reminder',
                'preferred_pickup_method',
                'pickup_notes',
                'total_items_picked_up',
                'last_pickup_date'
            ]);
        });
    }
};

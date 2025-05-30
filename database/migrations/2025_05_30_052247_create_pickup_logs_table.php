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
        Schema::create('pickup_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('pegawai_id');
            $table->enum('action', [
                'marked_for_pickup',
                'pickup_confirmed',
                'pickup_completed',
                'pickup_cancelled'
            ]);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
            
            // Indexes
            $table->index(['barang_id', 'created_at']);
            $table->index(['pegawai_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('merch', function (Blueprint $table) {
            $table->id('merch_id');
            $table->char('nama', 255)->nullable();
            $table->integer('jumlah_poin')->nullable();
            $table->integer('stock_merch')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merch');
    }
};

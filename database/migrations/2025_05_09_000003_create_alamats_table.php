<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('alamat_id');
            $table->foreignId('pembeli_id')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('status_default')->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organisasi', function (Blueprint $table) {
            $table->id('organisasi_id');
            $table->string('nama_organisasi')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisasi');
    }
};

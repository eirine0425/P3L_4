<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('garansi', function (Blueprint $table) {
            $table->id();
            $table->boolean('status'); // tinyint(1) -> boolean
            $table->dateTime('tanggal_aktif');
            $table->dateTime('tanggal_berakhir');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garansi');
    }
};

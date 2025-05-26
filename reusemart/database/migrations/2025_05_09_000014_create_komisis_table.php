<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('komisi', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable();
            $table->foreignId('penitip_id')->nullable();
            $table->foreignId('barang_id');
            $table->integer('persentase')->nullable();
            $table->integer('nominal_komisi')->nullable();
            $table->softDeletes();

            // optional: jadikan kombinasi unik jika diperlukan
            $table->primary(['barang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisi');
    }
};

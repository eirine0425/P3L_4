<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id('pegawai_id');
            $table->foreignId('user_id')->nullable();
            $table->char('nama_jabatan', 255)->nullable();
            $table->dateTime('tanggal_bergabung')->nullable();
            $table->integer('nominal_komisi')->nullable();
            $table->char('status_aktif', 25)->nullable();
            $table->string('nama')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};

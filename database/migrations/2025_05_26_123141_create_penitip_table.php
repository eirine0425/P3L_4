<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penitip', function (Blueprint $table) {
            $table->id('penitip_id');
            $table->string('nama');
            $table->integer('point_donasi')->default(0);
            $table->date('tanggal_registrasi')->nullable();
            $table->string('no_ktp', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('badge')->nullable();
            $table->string('periode')->nullable();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->softDeletes(); // Kolom deleted_at
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penitip');
    }
};


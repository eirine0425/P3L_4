<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penitip', function (Blueprint $table) {
            $table->id('penitip_id');
            $table->char('nama', 255)->nullable();
            $table->integer('point_donasi')->nullable();
            $table->dateTime('tanggal_registrasi')->nullable();
            $table->string('no_ktp')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('badge', 3)->default('no');
            $table->string('periode')->nullable();
            $table->string('saldo')->default(0);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penitip');
    }
};
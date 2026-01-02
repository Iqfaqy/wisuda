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
        Schema::create('wisudawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('prodi')->nullable();
            $table->string('fakultas')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('predikat', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('nama_ortu')->nullable();
            $table->integer('jumlah_tamu')->default(0);
            $table->integer('hari_wisuda')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisudawan');
    }
};

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
        Schema::create('kursi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kursi', 10);
            $table->char('section', 1); // A, B, C, D
            $table->integer('nomor'); // 1-100
            $table->integer('hari'); // 1 or 2
            $table->foreignId('wisudawan_id')->nullable()->constrained('wisudawan')->onDelete('set null');
            $table->enum('jenis_kelamin', ['L', 'P']); // For which gender this seat is designated
            $table->timestamps();

            $table->index(['section', 'hari']);
            $table->index('wisudawan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kursi');
    }
};

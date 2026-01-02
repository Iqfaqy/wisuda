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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisudawan_id')->constrained('wisudawan')->onDelete('cascade');
            $table->string('qr_code')->nullable();
            $table->timestamp('waktu_scan')->useCurrent();
            $table->timestamps();

            $table->unique('wisudawan_id'); // One attendance per wisudawan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};

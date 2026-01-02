<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wisudawan', function (Blueprint $table) {
            $table->string('judul_skripsi')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('ukuran_toga', 5)->nullable(); // S, M, L, XL
            // status_pembayaran biasanya diurus admin/otomatis, jadi tidak perlu di-input user manual
        });
    }

    public function down()
    {
        Schema::table('wisudawan', function (Blueprint $table) {
            $table->dropColumn(['judul_skripsi', 'nama_ayah', 'nama_ibu', 'ukuran_toga']);
        });
    }
};

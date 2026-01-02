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
        Schema::table('foto_wisuda', function (Blueprint $table) {
            $table->integer('hari')->default(1)->after('drive_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foto_wisuda', function (Blueprint $table) {
            $table->dropColumn('hari');
        });
    }
};

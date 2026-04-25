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
        Schema::create('cp_koleksi', function (Blueprint $table) {
            $table->integer('ID_CP_KOLEKSI', true)->unique('cp_koleksi_pk');
            $table->string('ISBN', 25)->nullable()->index('relation_1100_fk');
            $table->integer('ID_MST_LAPORAN')->nullable()->index('relation_2595_fk');
            $table->string('STATUS_BUKU', 100)->nullable();

            $table->primary(['ID_CP_KOLEKSI']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_koleksi');
    }
};

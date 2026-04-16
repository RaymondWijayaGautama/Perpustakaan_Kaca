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
        Schema::create('promo', function (Blueprint $table) {
            $table->integer('ID_PROMO')->primary();
            $table->string('NAMA_PROMO', 100)->nullable();
            $table->double('HARGA_PROMO')->nullable();
            $table->string('STATUS_PROMO', 100)->nullable();
            $table->dateTime('TGL_MULAI_PROMO')->nullable();
            $table->dateTime('TGL_SELESAI_PROMO')->nullable();

            $table->unique(['ID_PROMO'], 'promo_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo');
    }
};

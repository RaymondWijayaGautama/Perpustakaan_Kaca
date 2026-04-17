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
            $table->integer('id_promo')->primary();
            $table->string('nama_promo', 100)->nullable();
            $table->double('harga_promo')->nullable();
            $table->string('status_promo', 100)->nullable();
            $table->dateTime('tgl_mulai_promo')->nullable();
            $table->dateTime('tgl_selesai_promo')->nullable();

            $table->unique(['id_promo'], 'promo_pk');
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

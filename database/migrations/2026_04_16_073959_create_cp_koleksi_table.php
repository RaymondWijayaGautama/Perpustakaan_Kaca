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
            $table->integer('id_cp_koleksi')->unique('cp_koleksi_pk');
            $table->string('isbn', 25)->nullable()->index('relation_1100_fk');
            $table->integer('id_mst_laporan')->nullable()->index('relation_2595_fk');
            $table->string('status_buku', 100)->nullable();

            $table->primary(['id_cp_koleksi']);
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

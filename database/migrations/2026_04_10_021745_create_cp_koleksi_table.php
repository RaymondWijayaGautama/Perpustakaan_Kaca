<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('cp_koleksi', function (Blueprint $table) {
            $table->integer('id_cp_koleksi')->primary();
            $table->string('status_buku', 100);
            $table->string('ISBN', 25);
            $table->integer('id_mst_laporan');

            $table->foreign('ISBN')->references('ISBN')->on('mst_koleksi_buku');
            $table->foreign('id_mst_laporan')->references('id_mst_laporan')->on('mst_koleksi_laporan');
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

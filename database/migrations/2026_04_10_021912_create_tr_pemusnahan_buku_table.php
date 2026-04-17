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
         Schema::create('tr_pemusnahan_buku', function (Blueprint $table) {
            $table->integer('id_pemusnahan_buku')->autoIncrement()->primary();
            $table->string('ket_pemusnahan_buku', 255);
            $table->dateTime('tgl_pemusnahan_buku');
            $table->integer('id_cp_koleksi');

            $table->foreign('id_cp_koleksi')->references('id_cp_koleksi')->on('cp_koleksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pemusnahan_buku');
    }
};

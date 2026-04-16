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
        Schema::table('tr_pemusnahan_buku', function (Blueprint $table) {
            $table->foreign(['id_cp_koleksi'], 'tr_pemusnahan_buku_ibfk_1')->references(['id_cp_koleksi'])->on('cp_koleksi')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_pemusnahan_buku', function (Blueprint $table) {
            $table->dropForeign('tr_pemusnahan_buku_ibfk_1');
        });
    }
};

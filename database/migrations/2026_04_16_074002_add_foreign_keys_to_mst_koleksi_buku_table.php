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
        Schema::table('mst_koleksi_buku', function (Blueprint $table) {
            $table->foreign(['ID_REF_KOLEKSI'], 'mst_koleksi_buku_ibfk_1')->references(['ID_REF_KOLEKSI'])->on('ref_koleksi')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_koleksi_buku', function (Blueprint $table) {
            $table->dropForeign('mst_koleksi_buku_ibfk_1');
        });
    }
};

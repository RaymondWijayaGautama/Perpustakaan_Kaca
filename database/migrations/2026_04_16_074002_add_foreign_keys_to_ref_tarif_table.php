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
        Schema::table('ref_tarif', function (Blueprint $table) {
            $table->foreign(['id_jenis_tarif'], 'ref_tarif_ibfk_1')->references(['id_jenis_tarif'])->on('ref_jenis_tarif')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ta_anggaran'], 'ref_tarif_ibfk_2')->references(['id_ta_anggaran'])->on('ref_tahun_anggaran')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_tarif', function (Blueprint $table) {
            $table->dropForeign('ref_tarif_ibfk_1');
            $table->dropForeign('ref_tarif_ibfk_2');
        });
    }
};

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
        Schema::table('mst_program_kerja', function (Blueprint $table) {
            $table->foreign(['id_ta_anggaran'], 'mst_program_kerja_ibfk_1')->references(['id_ta_anggaran'])->on('ref_tahun_anggaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_unit'], 'mst_program_kerja_ibfk_2')->references(['id_unit'])->on('mst_unit')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tan'], 'mst_program_kerja_ibfk_3')->references(['id_tan'])->on('ref_tan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_master_coa'], 'mst_program_kerja_ibfk_4')->references(['id_master_coa'])->on('mst_coa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_kegiatan'], 'mst_program_kerja_ibfk_5')->references(['id_kegiatan'])->on('mst_kegiatan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_program_kerja', function (Blueprint $table) {
            $table->dropForeign('mst_program_kerja_ibfk_1');
            $table->dropForeign('mst_program_kerja_ibfk_2');
            $table->dropForeign('mst_program_kerja_ibfk_3');
            $table->dropForeign('mst_program_kerja_ibfk_4');
            $table->dropForeign('mst_program_kerja_ibfk_5');
        });
    }
};

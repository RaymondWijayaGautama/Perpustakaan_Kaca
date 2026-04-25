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
            $table->foreign(['ID_TA_ANGGARAN'], 'mst_program_kerja_ibfk_1')->references(['ID_TA_ANGGARAN'])->on('ref_tahun_anggaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_UNIT'], 'mst_program_kerja_ibfk_2')->references(['ID_UNIT'])->on('mst_unit')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_TAN'], 'mst_program_kerja_ibfk_3')->references(['ID_TAN'])->on('ref_tan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_MASTER_COA'], 'mst_program_kerja_ibfk_4')->references(['ID_MASTER_COA'])->on('mst_coa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_KEGIATAN'], 'mst_program_kerja_ibfk_5')->references(['ID_KEGIATAN'])->on('mst_kegiatan')->onUpdate('restrict')->onDelete('restrict');
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

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
        Schema::table('mst_mapel', function (Blueprint $table) {
            $table->foreign(['ID_KURIKULUM'], 'mst_mapel_ibfk_1')->references(['ID_KURIKULUM'])->on('mst_kurikulum')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_PROG_KEAHLIAN'], 'mst_mapel_ibfk_2')->references(['ID_PROG_KEAHLIAN'])->on('ref_program_keahlian')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_KONSENTRASI_KEAHLIAN'], 'mst_mapel_ibfk_3')->references(['ID_KONSENTRASI_KEAHLIAN'])->on('ref_konsentrasi_keahlian')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_mapel', function (Blueprint $table) {
            $table->dropForeign('mst_mapel_ibfk_1');
            $table->dropForeign('mst_mapel_ibfk_2');
            $table->dropForeign('mst_mapel_ibfk_3');
        });
    }
};

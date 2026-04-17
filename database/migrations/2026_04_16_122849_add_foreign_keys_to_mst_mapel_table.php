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
            $table->foreign(['id_kurikulum'], 'mst_mapel_ibfk_1')->references(['id_kurikulum'])->on('mst_kurikulum')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_prog_keahlian'], 'mst_mapel_ibfk_2')->references(['id_prog_keahlian'])->on('ref_program_keahlian')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_konsentrasi_keahlian'], 'mst_mapel_ibfk_3')->references(['id_konsentrasi_keahlian'])->on('ref_konsentrasi_keahlian')->onUpdate('restrict')->onDelete('restrict');
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

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
        Schema::table('tr_tracer', function (Blueprint $table) {
            $table->foreign(['ID_MST_PERTANYAAN'], 'tr_tracer_ibfk_1')->references(['ID_MST_PERTANYAAN'])->on('mst_pertanyaan_tracer')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_SISWA_TETAP'], 'tr_tracer_ibfk_2')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_tracer', function (Blueprint $table) {
            $table->dropForeign('tr_tracer_ibfk_1');
            $table->dropForeign('tr_tracer_ibfk_2');
        });
    }
};

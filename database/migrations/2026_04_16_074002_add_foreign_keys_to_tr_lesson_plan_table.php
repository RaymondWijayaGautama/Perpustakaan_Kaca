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
        Schema::table('tr_lesson_plan', function (Blueprint $table) {
            $table->foreign(['ID_ATP'], 'tr_lesson_plan_ibfk_1')->references(['ID_ATP'])->on('mst_arah_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_MAPEL'], 'tr_lesson_plan_ibfk_2')->references(['KODE_MAPEL'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_KELAS'], 'tr_lesson_plan_ibfk_3')->references(['ID_KELAS'])->on('mst_kelas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_lesson_plan', function (Blueprint $table) {
            $table->dropForeign('tr_lesson_plan_ibfk_1');
            $table->dropForeign('tr_lesson_plan_ibfk_2');
            $table->dropForeign('tr_lesson_plan_ibfk_3');
        });
    }
};

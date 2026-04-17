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
            $table->foreign(['id_atp'], 'tr_lesson_plan_ibfk_1')->references(['id_atp'])->on('mst_arah_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_mapel'], 'tr_lesson_plan_ibfk_2')->references(['kode_mapel'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_kelas'], 'tr_lesson_plan_ibfk_3')->references(['id_kelas'])->on('mst_kelas')->onUpdate('restrict')->onDelete('restrict');
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

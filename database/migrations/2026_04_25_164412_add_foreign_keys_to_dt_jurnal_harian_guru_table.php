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
        Schema::table('dt_jurnal_harian_guru', function (Blueprint $table) {
            $table->foreign(['ID_JURNAL_MENGAJAR'], 'dt_jurnal_harian_guru_ibfk_1')->references(['ID_JURNAL_MENGAJAR'])->on('tr_jurnal_mengajar')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_LESSON_PLAN'], 'dt_jurnal_harian_guru_ibfk_2')->references(['ID_LESSON_PLAN'])->on('tr_lesson_plan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_jurnal_harian_guru', function (Blueprint $table) {
            $table->dropForeign('dt_jurnal_harian_guru_ibfk_1');
            $table->dropForeign('dt_jurnal_harian_guru_ibfk_2');
        });
    }
};

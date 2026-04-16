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
        Schema::table('dtl_fpd', function (Blueprint $table) {
            $table->foreign(['ID_FPD'], 'dtl_fpd_ibfk_1')->references(['ID_FPD'])->on('fpd_anggaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_DT_PROGKER'], 'dtl_fpd_ibfk_2')->references(['ID_DT_PROGKER'])->on('dtl_program_kerja')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dtl_fpd', function (Blueprint $table) {
            $table->dropForeign('dtl_fpd_ibfk_1');
            $table->dropForeign('dtl_fpd_ibfk_2');
        });
    }
};

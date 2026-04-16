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
            $table->foreign(['id_fpd'], 'dtl_fpd_ibfk_1')->references(['id_fpd'])->on('fpd_anggaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_dt_progker'], 'dtl_fpd_ibfk_2')->references(['id_dt_progker'])->on('dtl_program_kerja')->onUpdate('restrict')->onDelete('restrict');
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

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
        Schema::table('dtl_program_kerja', function (Blueprint $table) {
            $table->foreign(['ID_PROGRAM_KERJA'], 'dtl_program_kerja_ibfk_1')->references(['ID_PROGRAM_KERJA'])->on('mst_program_kerja')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_REF_DANA'], 'dtl_program_kerja_ibfk_2')->references(['ID_REF_DANA'])->on('ref_sumber_dana')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dtl_program_kerja', function (Blueprint $table) {
            $table->dropForeign('dtl_program_kerja_ibfk_1');
            $table->dropForeign('dtl_program_kerja_ibfk_2');
        });
    }
};

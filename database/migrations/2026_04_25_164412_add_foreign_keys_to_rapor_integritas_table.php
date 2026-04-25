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
        Schema::table('rapor_integritas', function (Blueprint $table) {
            $table->foreign(['ID_REF_INTEGRITAS'], 'rapor_integritas_ibfk_1')->references(['ID_REF_INTEGRITAS'])->on('ref_integritas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_SISWA_TETAP'], 'rapor_integritas_ibfk_2')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapor_integritas', function (Blueprint $table) {
            $table->dropForeign('rapor_integritas_ibfk_1');
            $table->dropForeign('rapor_integritas_ibfk_2');
        });
    }
};

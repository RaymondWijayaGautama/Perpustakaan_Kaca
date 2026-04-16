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
            $table->foreign(['id_ref_integritas'], 'rapor_integritas_ibfk_1')->references(['id_ref_integritas'])->on('ref_integritas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_siswa_tetap'], 'rapor_integritas_ibfk_2')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
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

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
        Schema::table('dt_evaluasi_mandiri', function (Blueprint $table) {
            $table->foreign(['id_mst_evaluasi_mandiri'], 'dt_evaluasi_mandiri_ibfk_1')->references(['id_mst_evaluasi_mandiri'])->on('mst_evaluasi_mandiri')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tr_evaluasi_mandiri'], 'dt_evaluasi_mandiri_ibfk_2')->references(['id_tr_evaluasi_mandiri'])->on('tr_evaluasi_mandiri')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_evaluasi_mandiri', function (Blueprint $table) {
            $table->dropForeign('dt_evaluasi_mandiri_ibfk_1');
            $table->dropForeign('dt_evaluasi_mandiri_ibfk_2');
        });
    }
};

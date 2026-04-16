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
        Schema::table('dt_tr_pkg', function (Blueprint $table) {
            $table->foreign(['id_tr_pkg'], 'dt_tr_pkg_ibfk_1')->references(['id_tr_pkg'])->on('tr_pkg')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_mst_pkg'], 'dt_tr_pkg_ibfk_2')->references(['id_mst_pkg'])->on('mst_pkg')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_tr_pkg', function (Blueprint $table) {
            $table->dropForeign('dt_tr_pkg_ibfk_1');
            $table->dropForeign('dt_tr_pkg_ibfk_2');
        });
    }
};

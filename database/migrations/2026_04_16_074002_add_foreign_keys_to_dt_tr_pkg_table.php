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
            $table->foreign(['ID_TR_PKG'], 'dt_tr_pkg_ibfk_1')->references(['ID_TR_PKG'])->on('tr_pkg')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_MST_PKG'], 'dt_tr_pkg_ibfk_2')->references(['ID_MST_PKG'])->on('mst_pkg')->onUpdate('restrict')->onDelete('restrict');
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

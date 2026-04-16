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
        Schema::table('tr_stock_opname', function (Blueprint $table) {
            $table->foreign(['id_inventaris'], 'tr_stock_opname_ibfk_1')->references(['id_inventaris'])->on('mst_inventaris')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_stock_opname', function (Blueprint $table) {
            $table->dropForeign('tr_stock_opname_ibfk_1');
        });
    }
};

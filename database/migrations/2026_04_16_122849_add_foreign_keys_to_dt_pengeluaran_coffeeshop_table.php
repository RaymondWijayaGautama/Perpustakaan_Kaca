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
        Schema::table('dt_pengeluaran_coffeeshop', function (Blueprint $table) {
            $table->foreign(['id_tr_pengeluaran'], 'dt_pengeluaran_coffeeshop_ibfk_1')->references(['id_tr_pengeluaran'])->on('tr_pengeluaran_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_pengeluaran_coffeeshop', function (Blueprint $table) {
            $table->dropForeign('dt_pengeluaran_coffeeshop_ibfk_1');
        });
    }
};

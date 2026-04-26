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
        Schema::table('tr_cicilan', function (Blueprint $table) {
            $table->foreign(['ID_PEMBAYARAN'], 'tr_cicilan_ibfk_1')->references(['ID_PEMBAYARAN'])->on('tr_pembayaran')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_cicilan', function (Blueprint $table) {
            $table->dropForeign('tr_cicilan_ibfk_1');
        });
    }
};

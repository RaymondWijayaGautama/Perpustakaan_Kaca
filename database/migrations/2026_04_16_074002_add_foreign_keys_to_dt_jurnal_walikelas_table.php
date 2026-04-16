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
        Schema::table('dt_jurnal_walikelas', function (Blueprint $table) {
            $table->foreign(['id_jurnal_wali'], 'dt_jurnal_walikelas_ibfk_1')->references(['id_jurnal_wali'])->on('tr_jurnal_walikelas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_jurnal_walikelas', function (Blueprint $table) {
            $table->dropForeign('dt_jurnal_walikelas_ibfk_1');
        });
    }
};

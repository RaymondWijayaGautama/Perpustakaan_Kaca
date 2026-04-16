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
        Schema::table('tr_ijin_cuti', function (Blueprint $table) {
            $table->foreign(['nip_karyawan'], 'tr_ijin_cuti_ibfk_1')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_ijin_cuti', function (Blueprint $table) {
            $table->dropForeign('tr_ijin_cuti_ibfk_1');
        });
    }
};

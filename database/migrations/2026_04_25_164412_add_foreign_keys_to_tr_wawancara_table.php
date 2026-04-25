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
        Schema::table('tr_wawancara', function (Blueprint $table) {
            $table->foreign(['ID_PENDAFTARAN'], 'tr_wawancara_ibfk_1')->references(['ID_PENDAFTARAN'])->on('tr_pendaftaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIP_KARYAWAN'], 'tr_wawancara_ibfk_2')->references(['NIP_KARYAWAN'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_wawancara', function (Blueprint $table) {
            $table->dropForeign('tr_wawancara_ibfk_1');
            $table->dropForeign('tr_wawancara_ibfk_2');
        });
    }
};

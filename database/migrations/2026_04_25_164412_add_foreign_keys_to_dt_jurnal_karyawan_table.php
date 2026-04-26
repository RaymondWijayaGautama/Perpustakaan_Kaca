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
        Schema::table('dt_jurnal_karyawan', function (Blueprint $table) {
            $table->foreign(['ID_TR_JURNAL_KARYAWAN'], 'dt_jurnal_karyawan_ibfk_1')->references(['ID_TR_JURNAL_KARYAWAN'])->on('tr_jurnal_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_jurnal_karyawan', function (Blueprint $table) {
            $table->dropForeign('dt_jurnal_karyawan_ibfk_1');
        });
    }
};

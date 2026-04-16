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
        Schema::table('tr_jabatan', function (Blueprint $table) {
            $table->foreign(['id_jabatan'], 'tr_jabatan_ibfk_1')->references(['id_jabatan'])->on('ref_jabatan_str')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['nip_karyawan'], 'tr_jabatan_ibfk_2')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_jabatan', function (Blueprint $table) {
            $table->dropForeign('tr_jabatan_ibfk_1');
            $table->dropForeign('tr_jabatan_ibfk_2');
        });
    }
};

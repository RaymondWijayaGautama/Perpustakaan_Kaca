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
        Schema::table('tr_pkg', function (Blueprint $table) {
            $table->foreign(['nip_karyawan'], 'tr_pkg_ibfk_1')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_ta'], 'tr_pkg_ibfk_2')->references(['kode_ta'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_pkg', function (Blueprint $table) {
            $table->dropForeign('tr_pkg_ibfk_1');
            $table->dropForeign('tr_pkg_ibfk_2');
        });
    }
};

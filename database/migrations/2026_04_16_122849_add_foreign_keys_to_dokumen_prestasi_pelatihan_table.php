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
        Schema::table('dokumen_prestasi_pelatihan', function (Blueprint $table) {
            $table->foreign(['id_prestasi_pelatihan'], 'dokumen_prestasi_pelatihan_ibfk_1')->references(['id_prestasi_pelatihan'])->on('tr_prestasi_pelatihan_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_prestasi_pelatihan', function (Blueprint $table) {
            $table->dropForeign('dokumen_prestasi_pelatihan_ibfk_1');
        });
    }
};

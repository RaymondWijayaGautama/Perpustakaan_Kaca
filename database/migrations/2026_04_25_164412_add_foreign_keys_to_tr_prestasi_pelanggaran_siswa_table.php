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
        Schema::table('tr_prestasi_pelanggaran_siswa', function (Blueprint $table) {
            $table->foreign(['KODE_TA'], 'tr_prestasi_pelanggaran_siswa_ibfk_1')->references(['KODE_TA'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_prestasi_pelanggaran_siswa', function (Blueprint $table) {
            $table->dropForeign('tr_prestasi_pelanggaran_siswa_ibfk_1');
        });
    }
};

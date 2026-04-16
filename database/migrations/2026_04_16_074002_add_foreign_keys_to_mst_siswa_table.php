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
        Schema::table('mst_siswa', function (Blueprint $table) {
            $table->foreign(['ID_PENDAFTARAN'], 'mst_siswa_ibfk_1')->references(['ID_PENDAFTARAN'])->on('tr_pendaftaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_TA'], 'mst_siswa_ibfk_2')->references(['KODE_TA'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_siswa', function (Blueprint $table) {
            $table->dropForeign('mst_siswa_ibfk_1');
            $table->dropForeign('mst_siswa_ibfk_2');
        });
    }
};

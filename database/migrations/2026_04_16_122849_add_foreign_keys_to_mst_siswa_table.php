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
            $table->foreign(['id_pendaftaran'], 'mst_siswa_ibfk_1')->references(['id_pendaftaran'])->on('tr_pendaftaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_ta'], 'mst_siswa_ibfk_2')->references(['kode_ta'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
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

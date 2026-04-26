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
        Schema::table('dokumen_calon_siswa', function (Blueprint $table) {
            $table->foreign(['ID_PENDAFTARAN'], 'dokumen_calon_siswa_ibfk_1')->references(['ID_PENDAFTARAN'])->on('tr_pendaftaran')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_calon_siswa', function (Blueprint $table) {
            $table->dropForeign('dokumen_calon_siswa_ibfk_1');
        });
    }
};

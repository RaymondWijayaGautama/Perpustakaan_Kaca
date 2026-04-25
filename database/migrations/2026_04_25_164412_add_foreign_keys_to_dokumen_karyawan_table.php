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
        Schema::table('dokumen_karyawan', function (Blueprint $table) {
            $table->foreign(['NIP_KARYAWAN'], 'dokumen_karyawan_ibfk_1')->references(['NIP_KARYAWAN'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_karyawan', function (Blueprint $table) {
            $table->dropForeign('dokumen_karyawan_ibfk_1');
        });
    }
};

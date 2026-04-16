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
        Schema::table('fpd_anggaran', function (Blueprint $table) {
            $table->foreign(['id_program_kerja'], 'fpd_anggaran_ibfk_1')->references(['id_program_kerja'])->on('mst_program_kerja')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fpd_anggaran', function (Blueprint $table) {
            $table->dropForeign('fpd_anggaran_ibfk_1');
        });
    }
};

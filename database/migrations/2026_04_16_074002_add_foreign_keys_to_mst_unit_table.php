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
        Schema::table('mst_unit', function (Blueprint $table) {
            $table->foreign(['nip_karyawan'], 'mst_unit_ibfk_1')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_unit', function (Blueprint $table) {
            $table->dropForeign('mst_unit_ibfk_1');
        });
    }
};

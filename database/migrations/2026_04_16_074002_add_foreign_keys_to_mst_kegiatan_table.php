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
        Schema::table('mst_kegiatan', function (Blueprint $table) {
            $table->foreign(['MST_ID_KEGIATAN'], 'mst_kegiatan_ibfk_1')->references(['ID_KEGIATAN'])->on('mst_kegiatan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_kegiatan', function (Blueprint $table) {
            $table->dropForeign('mst_kegiatan_ibfk_1');
        });
    }
};

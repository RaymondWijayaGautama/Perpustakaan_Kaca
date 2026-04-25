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
        Schema::table('ref_kelurahan', function (Blueprint $table) {
            $table->foreign(['ID_KECAMATAN'], 'ref_kelurahan_ibfk_1')->references(['ID_KECAMATAN'])->on('ref_kecamatan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_kelurahan', function (Blueprint $table) {
            $table->dropForeign('ref_kelurahan_ibfk_1');
        });
    }
};

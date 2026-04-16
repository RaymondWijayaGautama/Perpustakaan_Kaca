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
        Schema::table('ref_kecamatan', function (Blueprint $table) {
            $table->foreign(['id_kota_kab'], 'ref_kecamatan_ibfk_1')->references(['id_kota_kab'])->on('ref_kota_kab')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_kecamatan', function (Blueprint $table) {
            $table->dropForeign('ref_kecamatan_ibfk_1');
        });
    }
};

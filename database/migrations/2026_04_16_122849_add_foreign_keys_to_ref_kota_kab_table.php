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
        Schema::table('ref_kota_kab', function (Blueprint $table) {
            $table->foreign(['id_provinsi'], 'ref_kota_kab_ibfk_1')->references(['id_provinsi'])->on('ref_provinsi')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_kota_kab', function (Blueprint $table) {
            $table->dropForeign('ref_kota_kab_ibfk_1');
        });
    }
};

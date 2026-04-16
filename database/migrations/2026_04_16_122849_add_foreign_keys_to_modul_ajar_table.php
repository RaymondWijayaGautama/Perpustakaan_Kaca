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
        Schema::table('modul_ajar', function (Blueprint $table) {
            $table->foreign(['id_kktp'], 'modul_ajar_ibfk_1')->references(['id_kktp'])->on('mst_kriteria_ketuntasan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modul_ajar', function (Blueprint $table) {
            $table->dropForeign('modul_ajar_ibfk_1');
        });
    }
};

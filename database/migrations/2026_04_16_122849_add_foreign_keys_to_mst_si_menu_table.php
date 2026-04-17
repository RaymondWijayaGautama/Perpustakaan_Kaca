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
        Schema::table('mst_si_menu', function (Blueprint $table) {
            $table->foreign(['id_si'], 'mst_si_menu_ibfk_1')->references(['id_si'])->on('mst_si')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_si_menu', function (Blueprint $table) {
            $table->dropForeign('mst_si_menu_ibfk_1');
        });
    }
};

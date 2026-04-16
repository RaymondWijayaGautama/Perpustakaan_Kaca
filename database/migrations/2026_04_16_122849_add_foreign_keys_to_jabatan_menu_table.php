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
        Schema::table('jabatan_menu', function (Blueprint $table) {
            $table->foreign(['id_si_role_menu'], 'jabatan_menu_ibfk_1')->references(['id_si_role_menu'])->on('mst_si_menu')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_jabatan'], 'jabatan_menu_ibfk_2')->references(['id_jabatan'])->on('ref_jabatan_str')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatan_menu', function (Blueprint $table) {
            $table->dropForeign('jabatan_menu_ibfk_1');
            $table->dropForeign('jabatan_menu_ibfk_2');
        });
    }
};

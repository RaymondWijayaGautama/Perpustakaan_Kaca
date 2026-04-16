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
        Schema::table('tr_pm', function (Blueprint $table) {
            $table->foreign(['id_program_kerja'], 'tr_pm_ibfk_1')->references(['id_program_kerja'])->on('mst_program_kerja')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ref_pm'], 'tr_pm_ibfk_2')->references(['id_ref_pm'])->on('ref_pm')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_pm', function (Blueprint $table) {
            $table->dropForeign('tr_pm_ibfk_1');
            $table->dropForeign('tr_pm_ibfk_2');
        });
    }
};

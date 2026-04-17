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
        Schema::table('mst_coa', function (Blueprint $table) {
            $table->foreign(['mst_id_master_coa'], 'mst_coa_ibfk_1')->references(['id_master_coa'])->on('mst_coa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_coa', function (Blueprint $table) {
            $table->dropForeign('mst_coa_ibfk_1');
        });
    }
};

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
        Schema::table('tr_penerimaan', function (Blueprint $table) {
            $table->foreign(['id_ref_penerimaan'], 'tr_penerimaan_ibfk_1')->references(['id_ref_penerimaan'])->on('ref_penerimaan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ref_dana'], 'tr_penerimaan_ibfk_2')->references(['id_ref_dana'])->on('ref_sumber_dana')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_penerimaan', function (Blueprint $table) {
            $table->dropForeign('tr_penerimaan_ibfk_1');
            $table->dropForeign('tr_penerimaan_ibfk_2');
        });
    }
};

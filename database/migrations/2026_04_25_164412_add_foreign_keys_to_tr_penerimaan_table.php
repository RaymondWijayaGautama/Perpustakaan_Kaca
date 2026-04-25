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
            $table->foreign(['ID_REF_PENERIMAAN'], 'tr_penerimaan_ibfk_1')->references(['ID_REF_PENERIMAAN'])->on('ref_penerimaan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_REF_DANA'], 'tr_penerimaan_ibfk_2')->references(['ID_REF_DANA'])->on('ref_sumber_dana')->onUpdate('restrict')->onDelete('restrict');
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

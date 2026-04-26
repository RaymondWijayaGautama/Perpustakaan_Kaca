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
        Schema::table('ref_penerimaan', function (Blueprint $table) {
            $table->foreign(['REF_ID_REF_PENERIMAAN'], 'ref_penerimaan_ibfk_1')->references(['ID_REF_PENERIMAAN'])->on('ref_penerimaan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_penerimaan', function (Blueprint $table) {
            $table->dropForeign('ref_penerimaan_ibfk_1');
        });
    }
};

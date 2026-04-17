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
            $table->foreign(['ref_id_ref_penerimaan'], 'ref_penerimaan_ibfk_1')->references(['id_ref_penerimaan'])->on('ref_penerimaan')->onUpdate('restrict')->onDelete('restrict');
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

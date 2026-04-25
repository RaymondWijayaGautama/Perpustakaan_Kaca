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
        Schema::table('mst_pertanyaan_tracer', function (Blueprint $table) {
            $table->foreign(['ID_TRACER'], 'mst_pertanyaan_tracer_ibfk_1')->references(['ID_TRACER'])->on('tr_tracer')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_pertanyaan_tracer', function (Blueprint $table) {
            $table->dropForeign('mst_pertanyaan_tracer_ibfk_1');
        });
    }
};

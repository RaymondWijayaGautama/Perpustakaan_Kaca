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
        Schema::table('pendaf_pkl', function (Blueprint $table) {
            $table->foreign(['kode_ta'], 'pendaf_pkl_ibfk_1')->references(['kode_ta'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaf_pkl', function (Blueprint $table) {
            $table->dropForeign('pendaf_pkl_ibfk_1');
        });
    }
};

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
        Schema::table('ref_sumber_dana', function (Blueprint $table) {
            $table->foreign(['REF_ID_REF_DANA'], 'ref_sumber_dana_ibfk_1')->references(['ID_REF_DANA'])->on('ref_sumber_dana')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_sumber_dana', function (Blueprint $table) {
            $table->dropForeign('ref_sumber_dana_ibfk_1');
        });
    }
};

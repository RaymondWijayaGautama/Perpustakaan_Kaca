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
        Schema::table('ref_pm', function (Blueprint $table) {
            $table->foreign(['ref_id_ref_pm'], 'ref_pm_ibfk_1')->references(['id_ref_pm'])->on('ref_pm')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_pm', function (Blueprint $table) {
            $table->dropForeign('ref_pm_ibfk_1');
        });
    }
};

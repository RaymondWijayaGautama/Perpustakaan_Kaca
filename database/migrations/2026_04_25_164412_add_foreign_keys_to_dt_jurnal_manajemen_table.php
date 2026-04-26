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
        Schema::table('dt_jurnal_manajemen', function (Blueprint $table) {
            $table->foreign(['ID_JURNAL_MANAJEMEN'], 'dt_jurnal_manajemen_ibfk_1')->references(['ID_JURNAL_MANAJEMEN'])->on('tr_jurnal_manajemen')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_jurnal_manajemen', function (Blueprint $table) {
            $table->dropForeign('dt_jurnal_manajemen_ibfk_1');
        });
    }
};

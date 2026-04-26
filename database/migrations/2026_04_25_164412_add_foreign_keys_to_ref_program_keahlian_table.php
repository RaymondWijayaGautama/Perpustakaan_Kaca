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
        Schema::table('ref_program_keahlian', function (Blueprint $table) {
            $table->foreign(['ID_KURIKULUM'], 'ref_program_keahlian_ibfk_1')->references(['ID_KURIKULUM'])->on('mst_kurikulum')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_program_keahlian', function (Blueprint $table) {
            $table->dropForeign('ref_program_keahlian_ibfk_1');
        });
    }
};

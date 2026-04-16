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
        Schema::create('mst_koleksi_laporan', function (Blueprint $table) {
            $table->integer('ID_MST_LAPORAN')->unique('mst_koleksi_laporan_pk');
            $table->integer('ID_PKL_SISWA')->nullable()->index('relation_9834_fk');
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_MST_LAPORAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_koleksi_laporan');
    }
};

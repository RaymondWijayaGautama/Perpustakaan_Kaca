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
            $table->integer('id_mst_laporan')->unique('mst_koleksi_laporan_pk');
            $table->integer('id_pkl_siswa')->nullable()->index('relation_9834_fk');
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_mst_laporan']);
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

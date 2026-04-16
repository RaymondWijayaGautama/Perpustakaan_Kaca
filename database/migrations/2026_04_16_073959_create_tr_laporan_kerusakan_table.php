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
        Schema::create('tr_laporan_kerusakan', function (Blueprint $table) {
            $table->integer('ID_TR_LAPORAN')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_5168_fk');
            $table->dateTime('TGL_LAPORAN_KERUSAKAN')->nullable();
            $table->string('KETERANGAN_LAPORAN')->nullable();

            $table->unique(['ID_TR_LAPORAN'], 'tr_laporan_kerusakan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_laporan_kerusakan');
    }
};

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
            $table->integer('id_tr_laporan')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_5168_fk');
            $table->dateTime('tgl_laporan_kerusakan')->nullable();
            $table->string('keterangan_laporan')->nullable();

            $table->unique(['id_tr_laporan'], 'tr_laporan_kerusakan_pk');
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

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
        Schema::create('tr_presensi_karyawan', function (Blueprint $table) {
            $table->integer('id_presensi_karyawan')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_9829_fk');
            $table->string('status_presensi_karyawan')->nullable();
            $table->dateTime('waktu_masuk')->nullable();
            $table->dateTime('waktu_keluar')->nullable();

            $table->unique(['id_presensi_karyawan'], 'tr_presensi_karyawan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_presensi_karyawan');
    }
};

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
            $table->integer('ID_PRESENSI_KARYAWAN', true);
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_9829_fk');
            $table->string('STATUS_PRESENSI_KARYAWAN')->nullable();
            $table->dateTime('WAKTU_MASUK')->nullable();
            $table->dateTime('WAKTU_KELUAR')->nullable();

            $table->unique(['ID_PRESENSI_KARYAWAN'], 'tr_presensi_karyawan_pk');
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

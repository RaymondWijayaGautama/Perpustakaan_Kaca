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
        Schema::create('presensi_siswa', function (Blueprint $table) {
            $table->integer('id_presensi_siswa')->unique('presensi_siswa_pk');
            $table->integer('id_tr_jadwal')->nullable()->index('relation_4231_fk');
            $table->integer('id_siswa_kelas')->nullable()->index('relation_4265_fk');
            $table->string('status_presensi_siswa', 100)->nullable();

            $table->primary(['id_presensi_siswa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_siswa');
    }
};

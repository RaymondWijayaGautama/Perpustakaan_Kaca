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
            $table->integer('ID_PRESENSI_SISWA')->unique('presensi_siswa_pk');
            $table->integer('ID_TR_JADWAL')->nullable()->index('relation_4231_fk');
            $table->integer('ID_SISWA_KELAS')->nullable()->index('relation_4265_fk');
            $table->string('STATUS_PRESENSI_SISWA', 100)->nullable();

            $table->primary(['ID_PRESENSI_SISWA']);
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

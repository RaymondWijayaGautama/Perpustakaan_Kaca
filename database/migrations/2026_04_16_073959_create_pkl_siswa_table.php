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
        Schema::create('pkl_siswa', function (Blueprint $table) {
            $table->integer('ID_PKL_SISWA')->unique('pkl_siswa_pk');
            $table->integer('ID_PENDAF_PKL')->nullable()->index('relation_4262_fk');
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_4279_fk');
            $table->integer('ID_MITRA_PKL')->nullable()->index('relation_4280_fk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_6034_fk');
            $table->string('STATUS_PKL', 100)->nullable();
            $table->float('NILAI_PKL')->nullable();
            $table->string('JUDUL_LAPORAN_PKL', 100)->nullable();
            $table->string('LINK_LAPORAN_PKL')->nullable();
            $table->string('LINK_GAMBAR_MAP')->nullable();

            $table->primary(['ID_PKL_SISWA']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_siswa');
    }
};

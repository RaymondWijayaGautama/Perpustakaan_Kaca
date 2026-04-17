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
            $table->integer('id_pkl_siswa')->unique('pkl_siswa_pk');
            $table->integer('id_pendaf_pkl')->nullable()->index('relation_4262_fk');
            $table->integer('id_siswa_tetap')->nullable()->index('relation_4279_fk');
            $table->integer('id_mitra_pkl')->nullable()->index('relation_4280_fk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_6034_fk');
            $table->string('status_pkl', 100)->nullable();
            $table->double('nilai_pkl')->nullable();
            $table->string('judul_laporan_pkl', 100)->nullable();
            $table->string('link_laporan_pkl')->nullable();
            $table->string('link_gambar_map')->nullable();

            $table->primary(['id_pkl_siswa']);
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

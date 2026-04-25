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
        Schema::create('mst_siswa', function (Blueprint $table) {
            $table->integer('ID_SISWA_TETAP', true)->unique('mst_siswa_pk');
            $table->integer('ID_PENDAFTARAN')->nullable()->index('relation_189_fk');
            $table->integer('KODE_TA')->nullable()->index('relation_191_fk');
            $table->char('KODE_CALON_SISWA', 20)->nullable();
            $table->char('NISN_SISWA', 10)->nullable();
            $table->string('NAMA_SISWA_TETAP', 100)->nullable();
            $table->dateTime('TGL_LAHIR_SISWA')->nullable();
            $table->string('TEMPAT_LAHIR_SISWA', 100)->nullable();
            $table->char('GENDER_SISWA', 10)->nullable();
            $table->char('GOLDAR_SISWA', 10)->nullable();
            $table->char('NO_HP_SISWA', 20)->nullable();
            $table->string('ALAMAT_JALAN_SISWA', 100)->nullable();
            $table->char('RT_SISWA', 3)->nullable();
            $table->char('RW_SISWA', 3)->nullable();
            $table->string('KELURAHAN_SISWA', 50)->nullable();
            $table->string('KECAMATAN_SISWA', 50)->nullable();
            $table->string('KOTA_KAB_SISWA', 50)->nullable();
            $table->string('PROVINSI_SISWA', 50)->nullable();
            $table->char('KODE_POS_SISWA', 6)->nullable();
            $table->char('NIK_SISWA', 20)->nullable();
            $table->string('TAHUN_LULUS', 4)->nullable();
            $table->string('PASSWORD_SISWA')->nullable();
            $table->string('NAMA_AYAH_SISWA')->nullable();
            $table->string('NAMA_IBU_SISWA')->nullable();
            $table->string('NAMA_WALI_SISWA')->nullable();
            $table->string('PEKERJAAN_AYAH_SISWA')->nullable();
            $table->string('PEKERJAAN_IBU_SISWA')->nullable();
            $table->string('PEKERJAAN_WALI_SISWA')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_SISWA_TETAP']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_siswa');
    }
};

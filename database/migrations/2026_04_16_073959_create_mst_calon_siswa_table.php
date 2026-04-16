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
        Schema::create('mst_calon_siswa', function (Blueprint $table) {
            $table->char('KODE_CALON', 20)->unique('mst_calon_siswa_pk');
            $table->integer('KODE_TA')->nullable()->index('relation_94_fk');
            $table->string('NAMA_CALON', 100)->nullable();
            $table->char('NISN_CALON', 10)->nullable();
            $table->dateTime('TGL_LAHIR_CALON')->nullable();
            $table->string('GENDER_CALON', 10)->nullable();
            $table->string('TEMPAT_LAHIR_CALON', 100)->nullable();
            $table->string('NO_HP_CALON', 20)->nullable();
            $table->char('GOLDAR_CALON', 10)->nullable();
            $table->string('ALAMAT_JALAN_CALON', 100)->nullable();
            $table->char('RT_CALON', 3)->nullable();
            $table->char('RW_CALON', 3)->nullable();
            $table->string('KELURAHAN_CALON', 100)->nullable();
            $table->string('KECAMATAN_CALON', 100)->nullable();
            $table->string('KOTA_KAB_CALON', 100)->nullable();
            $table->string('PROVINSI_CALON', 50)->nullable();
            $table->char('KODE_POS_CALON', 6)->nullable();
            $table->string('STATUS_TINGGAL', 100)->nullable();
            $table->char('NIK_CALON', 20)->nullable();
            $table->string('AGAMA_CALON', 25)->nullable();
            $table->string('ASAL_SEKOLAH')->nullable();
            $table->string('ALAMAT_ASAL_SEKOLAH')->nullable();
            $table->string('STATUS_ASAL_SEKOLAH', 100)->nullable();
            $table->string('NAMA_AYAH')->nullable();
            $table->string('NAMA_IBU')->nullable();
            $table->string('NAMA_WALI')->nullable();
            $table->string('PEKERJAAN_AYAH')->nullable();
            $table->string('PEKERJAAN_IBU')->nullable();
            $table->double('PENGHASILAN_AYAH')->nullable();
            $table->double('PENGHASILAN_IBU')->nullable();
            $table->double('JUMLAH_PENGHASILAN_ORTU')->nullable();
            $table->string('JARAK_RUMAH', 100)->nullable();
            $table->string('JENIS_TRANSPORTASI', 100)->nullable();
            $table->string('ALAMAT_WALI')->nullable();
            $table->string('PEKERJAAN_WALI')->nullable();
            $table->string('EMAIL_CALON_SISWA')->nullable();
            $table->string('PASSWORD_CALON_SISWA')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['KODE_CALON']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_calon_siswa');
    }
};

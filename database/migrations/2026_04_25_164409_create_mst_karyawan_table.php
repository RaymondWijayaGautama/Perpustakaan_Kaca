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
        Schema::create('mst_karyawan', function (Blueprint $table) {
            $table->string('NIP_KARYAWAN', 20)->unique('mst_karyawan_pk');
            $table->integer('ID_UNIT')->nullable()->index('relation_2400_fk2');
            $table->string('NAMA_KARYAWAN', 50)->nullable();
            $table->string('NAMA_LENGKAP_GELAR', 50)->nullable();
            $table->string('GOLONGAN_KARYAWAN', 25)->nullable();
            $table->string('JABATAN_FUNGSIONAL', 25)->nullable();
            $table->dateTime('TANGGAL_MASUK')->nullable();
            $table->string('STATUS_KEPEGAWAIAN', 100)->nullable();
            $table->string('NIK_KARYAWAN', 16)->nullable();
            $table->string('TEMPAT_LAHIR_KARYAWAN', 100)->nullable();
            $table->string('GENDER_KARYAWAN', 20)->nullable();
            $table->dateTime('TGL_LAHIR_KARYAWAN')->nullable();
            $table->string('ALAMAT_KARYAWAN')->nullable();
            $table->char('NO_HP_KARYAWAN', 15)->nullable();
            $table->string('EMAIL_KARYAWAN', 100)->nullable();
            $table->string('PASSWORD_KARYAWAN')->nullable();
            $table->string('PEND_TERAKHIR_KARYAWAN', 100)->nullable();
            $table->string('PRODI_KARYAWAN', 100)->nullable();
            $table->string('SERTIFIKAT_PENDIDIK', 100)->nullable();
            $table->string('LINK_FOTO_KARYAWAN')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['NIP_KARYAWAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_karyawan');
    }
};

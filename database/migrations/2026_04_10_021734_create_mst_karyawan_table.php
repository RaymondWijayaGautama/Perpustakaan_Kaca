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
            $table->string('nip_karyawan', 20)->primary();
            $table->string('nama_karyawan', 50);
            $table->string('nama_lengkap_gelar', 50);
            $table->string('golongan_karyawan', 25);
            $table->string('jabatan_fungsional', 25);
            $table->date('tanggal_masuk');
            $table->string('status_kepegawaian', 100);
            $table->string('nik_karyawan', 16);
            $table->string('tempat_lahir_karyawan', 100);
            $table->string('gender_karyawan', 20);
            $table->date('tgl_lahir_karyawan');
            $table->string('alamat_karyawan', 255);
            $table->char('no_hp_karyawan', 15);
            $table->string('email_karyawan', 100);
            $table->string('password_karyawan', 255);
            $table->string('pend_terakhir_karyawan', 100);
            $table->string('prodi_karyawan', 100);
            $table->string('sertifikat_pendidik', 100);
            $table->string('link_foto_karyawan', 100);
            $table->tinyInteger('is_delete');
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

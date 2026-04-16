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
            $table->string('nip_karyawan', 20)->unique('mst_karyawan_pk');
            $table->integer('id_unit')->nullable()->index('relation_2400_fk2');
            $table->string('nama_karyawan', 50)->nullable();
            $table->string('nama_lengkap_gelar', 50)->nullable();
            $table->string('golongan_karyawan', 25)->nullable();
            $table->string('jabatan_fungsional', 25)->nullable();
            $table->dateTime('tanggal_masuk')->nullable();
            $table->string('status_kepegawaian', 100)->nullable();
            $table->string('nik_karyawan', 16)->nullable();
            $table->string('tempat_lahir_karyawan', 100)->nullable();
            $table->string('gender_karyawan', 20)->nullable();
            $table->dateTime('tgl_lahir_karyawan')->nullable();
            $table->string('alamat_karyawan')->nullable();
            $table->char('no_hp_karyawan', 15)->nullable();
            $table->string('email_karyawan', 100)->nullable();
            $table->string('password_karyawan')->nullable();
            $table->string('pend_terakhir_karyawan', 100)->nullable();
            $table->string('prodi_karyawan', 100)->nullable();
            $table->string('sertifikat_pendidik', 100)->nullable();
            $table->string('link_foto_karyawan')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['nip_karyawan']);
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

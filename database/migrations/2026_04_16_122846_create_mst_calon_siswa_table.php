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
            $table->char('kode_calon', 20)->unique('mst_calon_siswa_pk');
            $table->integer('kode_ta')->nullable()->index('relation_94_fk');
            $table->string('nama_calon', 100)->nullable();
            $table->char('nisn_calon', 10)->nullable();
            $table->dateTime('tgl_lahir_calon')->nullable();
            $table->string('gender_calon', 10)->nullable();
            $table->string('tempat_lahir_calon', 100)->nullable();
            $table->string('no_hp_calon', 20)->nullable();
            $table->char('goldar_calon', 10)->nullable();
            $table->string('alamat_jalan_calon', 100)->nullable();
            $table->char('rt_calon', 3)->nullable();
            $table->char('rw_calon', 3)->nullable();
            $table->string('kelurahan_calon', 100)->nullable();
            $table->string('kecamatan_calon', 100)->nullable();
            $table->string('kota_kab_calon', 100)->nullable();
            $table->string('provinsi_calon', 50)->nullable();
            $table->char('kode_pos_calon', 6)->nullable();
            $table->string('status_tinggal', 100)->nullable();
            $table->char('nik_calon', 20)->nullable();
            $table->string('agama_calon', 25)->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('alamat_asal_sekolah')->nullable();
            $table->string('status_asal_sekolah', 100)->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->double('penghasilan_ayah')->nullable();
            $table->double('penghasilan_ibu')->nullable();
            $table->double('jumlah_penghasilan_ortu')->nullable();
            $table->string('jarak_rumah', 100)->nullable();
            $table->string('jenis_transportasi', 100)->nullable();
            $table->string('alamat_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('email_calon_siswa')->nullable();
            $table->string('password_calon_siswa')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['kode_calon']);
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

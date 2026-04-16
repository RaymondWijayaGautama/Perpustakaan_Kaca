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
            $table->integer('id_siswa_tetap', true)->unique('mst_siswa_pk');
            $table->integer('id_pendaftaran')->nullable()->index('relation_189_fk');
            $table->integer('kode_ta')->nullable()->index('relation_191_fk');
            $table->char('kode_calon_siswa', 20)->nullable();
            $table->char('nisn_siswa', 10)->nullable();
            $table->string('nama_siswa_tetap', 100)->nullable();
            $table->dateTime('tgl_lahir_siswa')->nullable();
            $table->string('tempat_lahir_siswa', 100)->nullable();
            $table->char('gender_siswa', 10)->nullable();
            $table->char('goldar_siswa', 10)->nullable();
            $table->char('no_hp_siswa', 20)->nullable();
            $table->string('alamat_jalan_siswa', 100)->nullable();
            $table->char('rt_siswa', 3)->nullable();
            $table->char('rw_siswa', 3)->nullable();
            $table->string('kelurahan_siswa', 50)->nullable();
            $table->string('kecamatan_siswa', 50)->nullable();
            $table->string('kota_kab_siswa', 50)->nullable();
            $table->string('provinsi_siswa', 50)->nullable();
            $table->char('kode_pos_siswa', 6)->nullable();
            $table->char('nik_siswa', 20)->nullable();
            $table->string('tahun_lulus', 4)->nullable();
            $table->string('password_siswa')->nullable();
            $table->string('nama_ayah_siswa')->nullable();
            $table->string('nama_ibu_siswa')->nullable();
            $table->string('nama_wali_siswa')->nullable();
            $table->string('pekerjaan_ayah_siswa')->nullable();
            $table->string('pekerjaan_ibu_siswa')->nullable();
            $table->string('pekerjaan_wali_siswa')->nullable();
            $table->string('nama_ortu_siswa', 100)->nullable();
            $table->string('nik_ortu_siswa', 16)->nullable();
            $table->string('peran_ortu_siswa', 50)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_siswa_tetap']);
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

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
            $table->id('id_siswa_tetap'); 
            $table->char('kode_calon_siswa', 20);
            $table->char('nisn_siswa', 10);
            $table->string('nama_siswa_tetap', 100);
            $table->date('tgl_lahir_siswa');
            $table->string('tempat_lahir_siswa', 100);
            $table->char('gender_siswa', 10);
            $table->char('goldar_siswa', 10);
            $table->char('no_hp_siswa', 20);
            $table->string('alamat_jalan_siswa', 100);
            $table->char('rt_siswa', 3);
            $table->char('rw_siswa', 3);
            $table->integer('kelurahan_siswa');
            $table->string('kecamatan_siswa', 50);
            $table->string('kota_kab_siswa', 50);
            $table->string('provinsi_siswa', 50);
            $table->char('kode_pos_siswa', 6);
            $table->char('nik_siswa', 20);
            $table->string('nama_ortu_siswa', 20);
            $table->char('nik_ortu_siswa', 20);
            $table->string('peran_ortu_siswa', 10);
            $table->string('tahun_lulus', 4);
            $table->string('password_siswa', 255);
            $table->tinyInteger('is_delete')->default(0);
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

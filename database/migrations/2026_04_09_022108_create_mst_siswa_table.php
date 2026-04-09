<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('mst_siswa', function (Blueprint $table) {
        $table->id('id_siswa_tetap'); // Auto-increment ID
        $table->string('kode_calon_siswa')->nullable();
        $table->string('nisn_siswa', 10)->unique();
        $table->string('nama_siswa_tetap');
        $table->date('tgl_lahir_siswa');
        $table->string('tempat_lahir_siswa');
        $table->enum('gender_siswa', ['Laki-laki', 'Perempuan']);
        $table->string('goldar_siswa', 2)->nullable();
        $table->string('no_hp_siswa', 15);
        $table->string('alamat_jalan_siswa');
        $table->string('rt_siswa', 3);
        $table->string('rw_siswa', 3);
        $table->integer('kelurahan_siswa');
        $table->string('kecamatan_siswa');
        $table->string('kota_kab_siswa');
        $table->string('provinsi_siswa');
        $table->string('kode_pos_siswa', 5);
        $table->string('nik_siswa', 16)->unique();
        $table->string('nama_ortu_siswa');
        $table->string('nik_ortu_siswa', 16);
        $table->string('peran_ortu_siswa');
        $table->year('tahun_lulus');
        $table->string('password_siswa');
        $table->boolean('is_delete')->default(0);
        $table->timestamps();
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

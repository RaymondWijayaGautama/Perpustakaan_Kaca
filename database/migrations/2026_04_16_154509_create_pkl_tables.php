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

        // 1. Tabel Pendaftaran PKL

        Schema::create('pendaf_pkl', function (Blueprint $table) {

            $table->integer('id_pendaf_pkl')->autoIncrement();

            $table->integer('kode_ta')->nullable();

            $table->dateTime('tgl_mulai_pkl')->nullable();

            $table->dateTime('tgl_selesai_pkl')->nullable();

            $table->string('status_pkl', 100)->nullable();

        });


        // 2. Tabel Mitra PKL (Perusahaan/Instansi)

        Schema::create('mitra_pkl', function (Blueprint $table) {

            $table->integer('id_mitra_pkl')->autoIncrement();

            $table->string('nama_mitra_pkl', 255)->nullable();

            $table->string('status_mitra_pkl', 100)->nullable();

            $table->string('alamat_mitra_pkl', 255)->nullable();

            $table->string('no_telp_mitra_pkl', 255)->nullable();

            $table->string('jarak_tempat_pkl', 100)->nullable();

            $table->string('no_mou_pkl', 100)->nullable();

        });


        // 3. Tabel Data PKL Siswa

        Schema::create('pkl_siswa', function (Blueprint $table) {

            $table->integer('id_pkl_siswa')->autoIncrement();

            $table->integer('id_pendaf_pkl')->nullable();

            $table->integer('id_siswa_tetap')->nullable();

            $table->integer('id_mitra_pkl')->nullable();

            $table->string('nip_karyawan', 20)->nullable(); // Pembimbing

            $table->string('status_pkl', 100)->nullable();

            $table->float('nilai_pkl')->nullable();

            $table->string('judul_laporan_pkl', 100)->nullable();

            $table->string('link_laporan_pkl', 255)->nullable();

            $table->string('link_gambar_map', 255)->nullable();

        });

        

        // mst_koleksi_laporan Dihapus dari sini karena sudah ada di migration tanggal 10 April

    }


    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        Schema::dropIfExists('pkl_siswa');

        Schema::dropIfExists('mitra_pkl');

        Schema::dropIfExists('pendaf_pkl');

    }

};  
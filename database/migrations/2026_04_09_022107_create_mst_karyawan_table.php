<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('mst_karyawan', function (Blueprint $table) {
        $table->string('nip_karyawan')->primary(); // NIP sebagai Primary Key
        $table->string('nama_karyawan');
        $table->string('nama_lengkap_gelar');
        $table->string('golongan_karyawan', 10);
        $table->string('jabatan_fungsional');
        $table->date('tanggal_masuk');
        $table->string('status_kepegawaian');
        $table->string('nik_karyawan', 16)->unique();
        $table->string('tempat_lahir_karyawan');
        $table->enum('gender_karyawan', ['Laki-laki', 'Perempuan']);
        $table->date('tgl_lahir_karyawan');
        $table->text('alamat_karyawan');
        $table->string('no_hp_karyawan', 15);
        $table->string('email_karyawan')->unique();
        $table->string('password_karyawan');
        $table->string('pend_terakhir_karyawan');
        $table->string('prodi_karyawan');
        $table->string('sertifikat_pendidik')->default('-');
        $table->string('link_foto_karyawan')->default('default.jpg');
        $table->boolean('is_delete')->default(0);
        
        // Tambahkan ini jika ingin menggunakan timestamps, 
        // atau hapus jika model menggunakan public $timestamps = false;
        $table->timestamps(); 
    });
}

public function down(): void
{
    Schema::dropIfExists('mst_karyawan');
}
};

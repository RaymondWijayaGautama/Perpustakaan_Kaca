<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tr_booking', function (Blueprint $table) {
            $table->id('id_booking'); // Primary Key
            $table->unsignedBigInteger('id_cp_koleksi'); // ID Fisik Buku
            $table->unsignedBigInteger('id_siswa_tetap'); // ID Siswa
            $table->dateTime('tgl_booking');
            $table->dateTime('expired_at')->nullable();
            $table->string('status_booking')->default('Aktif'); // Aktif, Dibatalkan, Selesai
            $table->text('keterangan')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_booking');
    }
};
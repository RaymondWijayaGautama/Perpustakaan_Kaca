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
        Schema::create('tr_jadwal', function (Blueprint $table) {
            $table->integer('id_tr_jadwal')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_4271_fk');
            $table->char('kode_mapel', 10)->nullable()->index('relation_4272_fk');
            $table->integer('id_kelas')->nullable()->index('relation_9839_fk');
            $table->string('hari_jadwal', 10)->nullable();
            $table->dateTime('tgl_jadwal')->nullable();
            $table->string('ruangan_mapel', 100)->nullable();
            $table->time('jam_mulai_mapel')->nullable();
            $table->time('jam_selesai_mapel')->nullable();

            $table->unique(['id_tr_jadwal'], 'tr_jadwal_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jadwal');
    }
};

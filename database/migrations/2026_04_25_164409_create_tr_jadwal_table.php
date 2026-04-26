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
            $table->integer('ID_TR_JADWAL', true);
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_4271_fk');
            $table->char('KODE_MAPEL', 10)->nullable()->index('relation_4272_fk');
            $table->integer('ID_KELAS')->nullable()->index('relation_9839_fk');
            $table->string('HARI_JADWAL', 10)->nullable();
            $table->dateTime('TGL_JADWAL')->nullable();
            $table->string('RUANGAN_MAPEL', 100)->nullable();
            $table->time('JAM_MULAI_MAPEL')->nullable();
            $table->time('JAM_SELESAI_MAPEL')->nullable();

            $table->unique(['ID_TR_JADWAL'], 'tr_jadwal_pk');
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

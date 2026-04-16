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
        Schema::create('jadwal_coffeeshop', function (Blueprint $table) {
            $table->integer('id_jadwal_coffeeshop')->unique('jadwal_coffeeshop_pk');
            $table->integer('id_role_coffeeshop')->nullable()->index('relation_136_fk');
            $table->integer('id_siswa_tetap')->nullable()->index('relation_3379_fk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_3380_fk');
            $table->integer('id_nilai_kinerja')->index('relation_6921_fk2');
            $table->dateTime('tanggal_jadwal_coffeeshop')->nullable();
            $table->string('status_presensi', 100)->nullable();
            $table->string('nip_validator_presensi', 20)->nullable();

            $table->primary(['id_jadwal_coffeeshop']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_coffeeshop');
    }
};

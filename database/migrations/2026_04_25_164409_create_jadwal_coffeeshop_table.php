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
            $table->integer('ID_JADWAL_COFFEESHOP', true)->unique('jadwal_coffeeshop_pk');
            $table->integer('ID_ROLE_COFFEESHOP')->nullable()->index('relation_136_fk');
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_3379_fk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_3380_fk');
            $table->integer('ID_NILAI_KINERJA')->index('relation_6921_fk2');
            $table->dateTime('TANGGAL_JADWAL_COFFEESHOP')->nullable();
            $table->string('STATUS_PRESENSI', 100)->nullable();
            $table->string('NIP_VALIDATOR_PRESENSI', 20)->nullable();

            $table->primary(['ID_JADWAL_COFFEESHOP']);
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

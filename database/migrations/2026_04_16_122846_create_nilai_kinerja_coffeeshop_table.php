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
        Schema::create('nilai_kinerja_coffeeshop', function (Blueprint $table) {
            $table->integer('id_nilai_kinerja')->unique('nilai_kinerja_coffeeshop_pk');
            $table->integer('id_siswa_tetap')->nullable()->index('relation_3454_fk');
            $table->integer('id_jadwal_coffeeshop')->nullable()->index('relation_6921_fk');
            $table->string('ket_kinerja')->nullable();
            $table->double('nilai_kinerja')->nullable();
            $table->string('nip_validator_koordinator', 20)->nullable();

            $table->primary(['id_nilai_kinerja']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kinerja_coffeeshop');
    }
};

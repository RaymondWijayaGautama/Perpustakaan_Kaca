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
            $table->integer('ID_NILAI_KINERJA')->unique('nilai_kinerja_coffeeshop_pk');
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_3454_fk');
            $table->integer('ID_JADWAL_COFFEESHOP')->nullable()->index('relation_6921_fk');
            $table->string('KET_KINERJA')->nullable();
            $table->float('NILAI_KINERJA')->nullable();
            $table->string('NIP_VALIDATOR_KOORDINATOR', 20)->nullable();

            $table->primary(['ID_NILAI_KINERJA']);
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

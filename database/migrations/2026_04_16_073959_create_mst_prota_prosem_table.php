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
        Schema::create('mst_prota_prosem', function (Blueprint $table) {
            $table->integer('ID_PROTA_PROSEM')->unique('mst_prota_prosem_pk');
            $table->integer('ID_ATP')->nullable()->index('relation_5986_fk');
            $table->char('KODE_MAPEL', 10)->nullable()->index('relation_6029_fk');
            $table->string('POKOK_MATERI', 100)->nullable();
            $table->float('ALOKASI_JP')->nullable();
            $table->string('ALOKASI_TA', 10)->nullable();
            $table->string('ALOKASI_SEMESTER', 10)->nullable();
            $table->string('ALOKASI_BULAN', 100)->nullable();
            $table->integer('ALOKASI_MINGGU')->nullable();
            $table->string('NIP_VALIDATOR_PROTA_PROSEM', 20)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_PROTA_PROSEM']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_prota_prosem');
    }
};

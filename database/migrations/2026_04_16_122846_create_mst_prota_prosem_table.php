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
            $table->integer('id_prota_prosem')->unique('mst_prota_prosem_pk');
            $table->integer('id_atp')->nullable()->index('relation_5986_fk');
            $table->char('kode_mapel', 10)->nullable()->index('relation_6029_fk');
            $table->string('pokok_materi', 100)->nullable();
            $table->double('alokasi_jp')->nullable();
            $table->string('alokasi_ta', 10)->nullable();
            $table->string('alokasi_semester', 10)->nullable();
            $table->string('alokasi_bulan', 100)->nullable();
            $table->integer('alokasi_minggu')->nullable();
            $table->string('nip_validator_prota_prosem', 20)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_prota_prosem']);
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

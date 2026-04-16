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
        Schema::create('hst_kelas', function (Blueprint $table) {
            $table->integer('id_hst_kelas')->unique('hst_kelas_pk');
            $table->integer('id_siswa_tetap')->nullable()->index('relation_433_fk');
            $table->integer('kode_ta')->nullable()->index('relation_434_fk');
            $table->char('kelas', 10)->nullable();

            $table->primary(['id_hst_kelas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hst_kelas');
    }
};

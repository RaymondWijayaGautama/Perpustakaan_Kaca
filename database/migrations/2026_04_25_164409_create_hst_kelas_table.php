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
            $table->integer('ID_HST_KELAS', true)->unique('hst_kelas_pk');
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_433_fk');
            $table->integer('KODE_TA')->nullable()->index('relation_434_fk');
            $table->char('KELAS', 10)->nullable();

            $table->primary(['ID_HST_KELAS']);
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

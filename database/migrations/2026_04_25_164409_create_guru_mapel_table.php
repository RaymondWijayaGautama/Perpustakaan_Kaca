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
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->integer('ID_GURU_MAPEL', true)->unique('guru_mapel_pk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_477_fk');
            $table->char('KODE_MAPEL', 10)->nullable()->index('relation_478_fk');

            $table->primary(['ID_GURU_MAPEL']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};

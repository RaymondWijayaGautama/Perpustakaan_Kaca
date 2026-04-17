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
            $table->integer('id_guru_mapel')->unique('guru_mapel_pk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_477_fk');
            $table->char('kode_mapel', 10)->nullable()->index('relation_478_fk');

            $table->primary(['id_guru_mapel']);
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

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
        Schema::create('dt_nilai', function (Blueprint $table) {
            $table->integer('id_dt_nilai')->unique('dt_nilai_pk');
            $table->integer('id_siswa_kelas')->nullable()->index('relation_4276_fk');
            $table->char('kode_mapel', 10)->nullable()->index('relation_4277_fk');
            $table->string('jenis_nilai', 100)->nullable();
            $table->double('nilai_komponen')->nullable();

            $table->primary(['id_dt_nilai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_nilai');
    }
};

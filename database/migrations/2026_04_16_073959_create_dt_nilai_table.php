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
            $table->integer('ID_DT_NILAI')->unique('dt_nilai_pk');
            $table->integer('ID_SISWA_KELAS')->nullable()->index('relation_4276_fk');
            $table->char('KODE_MAPEL', 10)->nullable()->index('relation_4277_fk');
            $table->string('JENIS_NILAI', 100)->nullable();
            $table->float('NILAI_KOMPONEN')->nullable();

            $table->primary(['ID_DT_NILAI']);
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

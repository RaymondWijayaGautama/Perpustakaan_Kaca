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
        Schema::create('modul_ajar', function (Blueprint $table) {
            $table->integer('ID_MODUL_AJAR', true)->unique('modul_ajar_pk');
            $table->integer('ID_KKTP')->nullable()->index('relation_6028_fk');
            $table->string('JUDUL_MODUL')->nullable();
            $table->char('KEGIATAN_AWAL', 254)->nullable();
            $table->char('KEGIATAN_INTI', 254)->nullable();
            $table->char('KEGIATAN_PENUTUP', 254)->nullable();
            $table->char('LAMPIRAN_MODUL', 254)->nullable();

            $table->primary(['ID_MODUL_AJAR']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modul_ajar');
    }
};

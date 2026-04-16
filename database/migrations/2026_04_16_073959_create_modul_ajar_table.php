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
            $table->integer('id_modul_ajar')->unique('modul_ajar_pk');
            $table->integer('id_kktp')->nullable()->index('relation_6028_fk');
            $table->string('judul_modul')->nullable();
            $table->char('kegiatan_awal', 254)->nullable();
            $table->char('kegiatan_inti', 254)->nullable();
            $table->char('kegiatan_penutup', 254)->nullable();
            $table->char('lampiran_modul', 254)->nullable();

            $table->primary(['id_modul_ajar']);
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

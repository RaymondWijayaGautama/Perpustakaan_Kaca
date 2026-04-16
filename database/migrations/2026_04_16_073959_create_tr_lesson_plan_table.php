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
        Schema::create('tr_lesson_plan', function (Blueprint $table) {
            $table->integer('id_lesson_plan')->primary();
            $table->integer('id_atp')->nullable()->index('relation_4317_fk');
            $table->char('kode_mapel', 10)->nullable()->index('relation_4318_fk');
            $table->integer('id_kelas')->nullable()->index('relation_8749_fk');
            $table->dateTime('tgl_mulai_lesson_plan')->nullable();
            $table->dateTime('tgl_selesai_lesson_plan')->nullable();
            $table->string('materi_pelajaran')->nullable();
            $table->string('status_lesson_plan', 100)->nullable();
            $table->string('nip_validator_lesson_plan', 20)->nullable();

            $table->unique(['id_lesson_plan'], 'tr_lesson_plan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_lesson_plan');
    }
};

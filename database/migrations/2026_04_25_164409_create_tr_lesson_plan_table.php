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
            $table->integer('ID_LESSON_PLAN', true);
            $table->integer('ID_ATP')->nullable()->index('relation_4317_fk');
            $table->char('KODE_MAPEL', 10)->nullable()->index('relation_4318_fk');
            $table->integer('ID_KELAS')->nullable()->index('relation_8749_fk');
            $table->dateTime('TGL_MULAI_LESSON_PLAN')->nullable();
            $table->dateTime('TGL_SELESAI_LESSON_PLAN')->nullable();
            $table->string('MATERI_PELAJARAN')->nullable();
            $table->string('STATUS_LESSON_PLAN', 100)->nullable();
            $table->string('NIP_VALIDATOR_LESSON_PLAN', 20)->nullable();

            $table->unique(['ID_LESSON_PLAN'], 'tr_lesson_plan_pk');
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

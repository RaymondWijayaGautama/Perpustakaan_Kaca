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
        Schema::create('pendaf_pkl', function (Blueprint $table) {
            $table->integer('ID_PENDAF_PKL')->unique('pendaf_pkl_pk');
            $table->integer('KODE_TA')->nullable()->index('relation_4278_fk');
            $table->dateTime('TGL_MULAI_PKL')->nullable();
            $table->dateTime('TGL_SELESAI_PKL')->nullable();
            $table->string('STATUS_PKL', 100)->nullable();

            $table->primary(['ID_PENDAF_PKL']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaf_pkl');
    }
};

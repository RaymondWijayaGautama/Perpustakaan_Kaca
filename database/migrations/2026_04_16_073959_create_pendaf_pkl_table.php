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
            $table->integer('id_pendaf_pkl')->unique('pendaf_pkl_pk');
            $table->integer('kode_ta')->nullable()->index('relation_4278_fk');
            $table->dateTime('tgl_mulai_pkl')->nullable();
            $table->dateTime('tgl_selesai_pkl')->nullable();
            $table->string('status_pkl', 100)->nullable();

            $table->primary(['id_pendaf_pkl']);
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

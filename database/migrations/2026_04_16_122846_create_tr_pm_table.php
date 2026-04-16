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
        Schema::create('tr_pm', function (Blueprint $table) {
            $table->integer('id_pm')->primary();
            $table->integer('id_program_kerja')->nullable()->index('relation_1766_fk');
            $table->integer('id_ref_pm')->nullable()->index('relation_1789_fk');
            $table->dateTime('tgl_pm')->nullable();
            $table->string('deskripsi_tr_pm', 100)->nullable();

            $table->unique(['id_pm'], 'tr_pm_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pm');
    }
};

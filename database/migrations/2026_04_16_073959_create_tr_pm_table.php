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
            $table->integer('ID_PM')->primary();
            $table->integer('ID_PROGRAM_KERJA')->nullable()->index('relation_1766_fk');
            $table->integer('ID_REF_PM')->nullable()->index('relation_1789_fk');
            $table->dateTime('TGL_PM')->nullable();
            $table->string('DESKRIPSI_TR_PM', 100)->nullable();

            $table->unique(['ID_PM'], 'tr_pm_pk');
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

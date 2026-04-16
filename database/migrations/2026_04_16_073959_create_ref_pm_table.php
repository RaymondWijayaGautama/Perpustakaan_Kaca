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
        Schema::create('ref_pm', function (Blueprint $table) {
            $table->integer('ID_REF_PM')->primary();
            $table->integer('REF_ID_REF_PM')->nullable()->index('relation_1798_fk');
            $table->char('NAMA_PM', 25)->nullable();
            $table->string('DESKRIPSI_PM', 100)->nullable();

            $table->unique(['ID_REF_PM'], 'ref_pm_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_pm');
    }
};

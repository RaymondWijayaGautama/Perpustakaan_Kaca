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
            $table->integer('id_ref_pm')->primary();
            $table->integer('ref_id_ref_pm')->nullable()->index('relation_1798_fk');
            $table->char('nama_pm', 25)->nullable();
            $table->string('deskripsi_pm', 100)->nullable();

            $table->unique(['id_ref_pm'], 'ref_pm_pk');
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

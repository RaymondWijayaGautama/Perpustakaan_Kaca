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
        Schema::create('ref_penerimaan', function (Blueprint $table) {
            $table->integer('ID_REF_PENERIMAAN')->primary();
            $table->integer('REF_ID_REF_PENERIMAAN')->nullable()->index('relation_5994_fk');
            $table->string('DESKRIPSI_REF_PENERIMAAN', 100)->nullable();

            $table->unique(['ID_REF_PENERIMAAN'], 'ref_penerimaan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_penerimaan');
    }
};

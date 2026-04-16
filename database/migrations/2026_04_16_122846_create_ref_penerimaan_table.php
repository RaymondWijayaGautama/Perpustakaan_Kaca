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
            $table->integer('id_ref_penerimaan')->primary();
            $table->integer('ref_id_ref_penerimaan')->nullable()->index('relation_5994_fk');
            $table->string('deskripsi_ref_penerimaan', 100)->nullable();

            $table->unique(['id_ref_penerimaan'], 'ref_penerimaan_pk');
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

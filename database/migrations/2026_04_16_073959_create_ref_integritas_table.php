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
        Schema::create('ref_integritas', function (Blueprint $table) {
            $table->integer('ID_REF_INTEGRITAS')->primary();
            $table->integer('REF_ID_REF_INTEGRITAS')->nullable()->index('relation_1126_fk');

            $table->unique(['ID_REF_INTEGRITAS'], 'ref_integritas_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_integritas');
    }
};

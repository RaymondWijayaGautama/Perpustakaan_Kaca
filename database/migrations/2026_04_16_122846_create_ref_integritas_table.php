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
            $table->integer('id_ref_integritas')->primary();
            $table->integer('ref_id_ref_integritas')->nullable()->index('relation_1126_fk');

            $table->unique(['id_ref_integritas'], 'ref_integritas_pk');
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

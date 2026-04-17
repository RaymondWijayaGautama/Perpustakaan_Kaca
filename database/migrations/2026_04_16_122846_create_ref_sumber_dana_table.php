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
        Schema::create('ref_sumber_dana', function (Blueprint $table) {
            $table->integer('id_ref_dana')->primary();
            $table->integer('ref_id_ref_dana')->nullable()->index('relation_1796_fk');
            $table->string('deskripsi_sumber_dana', 100)->nullable();

            $table->unique(['id_ref_dana'], 'ref_sumber_dana_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_sumber_dana');
    }
};

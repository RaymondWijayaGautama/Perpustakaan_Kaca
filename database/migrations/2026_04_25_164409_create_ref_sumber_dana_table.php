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
            $table->integer('ID_REF_DANA', true);
            $table->integer('REF_ID_REF_DANA')->nullable()->index('relation_1796_fk');
            $table->string('DESKRIPSI_SUMBER_DANA', 100)->nullable();

            $table->unique(['ID_REF_DANA'], 'ref_sumber_dana_pk');
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

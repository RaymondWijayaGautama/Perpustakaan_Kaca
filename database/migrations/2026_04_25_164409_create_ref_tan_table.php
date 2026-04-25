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
        Schema::create('ref_tan', function (Blueprint $table) {
            $table->integer('ID_TAN', true);
            $table->char('TAHUN', 4)->nullable();
            $table->boolean('IS_CURRENT')->nullable();
            $table->string('DESKRIPSI_TAN', 100)->nullable();

            $table->unique(['ID_TAN'], 'ref_tan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_tan');
    }
};

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
            $table->integer('id_tan')->primary();
            $table->char('tahun', 4)->nullable();
            $table->boolean('is_current')->nullable();
            $table->string('deskripsi_tan', 100)->nullable();

            $table->unique(['id_tan'], 'ref_tan_pk');
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

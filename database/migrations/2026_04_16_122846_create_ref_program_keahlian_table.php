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
        Schema::create('ref_program_keahlian', function (Blueprint $table) {
            $table->integer('id_prog_keahlian')->primary();
            $table->integer('id_kurikulum')->nullable()->index('relation_229_fk');
            $table->string('nama_prog_keahlian')->nullable();

            $table->unique(['id_prog_keahlian'], 'ref_program_keahlian_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_program_keahlian');
    }
};

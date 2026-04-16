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
            $table->integer('ID_PROG_KEAHLIAN')->primary();
            $table->integer('ID_KURIKULUM')->nullable()->index('relation_229_fk');
            $table->string('NAMA_PROG_KEAHLIAN')->nullable();

            $table->unique(['ID_PROG_KEAHLIAN'], 'ref_program_keahlian_pk');
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

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
        Schema::create('ref_konsentrasi_keahlian', function (Blueprint $table) {
            $table->integer('id_konsentrasi_keahlian')->primary();
            $table->integer('id_prog_keahlian')->nullable()->index('relation_235_fk');
            $table->string('nama_konsentrasi_keahlian')->nullable();

            $table->unique(['id_konsentrasi_keahlian'], 'ref_konsentrasi_keahlian_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_konsentrasi_keahlian');
    }
};

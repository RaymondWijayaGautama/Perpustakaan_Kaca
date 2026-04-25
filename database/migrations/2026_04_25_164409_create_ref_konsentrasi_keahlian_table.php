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
            $table->integer('ID_KONSENTRASI_KEAHLIAN', true);
            $table->integer('ID_PROG_KEAHLIAN')->nullable()->index('relation_235_fk');
            $table->string('NAMA_KONSENTRASI_KEAHLIAN')->nullable();

            $table->unique(['ID_KONSENTRASI_KEAHLIAN'], 'ref_konsentrasi_keahlian_pk');
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

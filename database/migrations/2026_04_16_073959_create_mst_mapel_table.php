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
        Schema::create('mst_mapel', function (Blueprint $table) {
            $table->char('KODE_MAPEL', 10)->unique('mst_mapel_pk');
            $table->integer('ID_KURIKULUM')->nullable()->index('relation_223_fk');
            $table->integer('ID_PROG_KEAHLIAN')->nullable()->index('relation_228_fk');
            $table->integer('ID_KONSENTRASI_KEAHLIAN')->nullable()->index('relation_234_fk');
            $table->char('KATEGORI_KEJURUAN', 10)->nullable();
            $table->string('NAMA_MAPEL', 25)->nullable();
            $table->float('JP')->nullable();
            $table->float('TOTAL_JP')->nullable();
            $table->char('FASE', 10)->nullable();
            $table->string('SEMESTER_MAPEL', 10)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['KODE_MAPEL']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_mapel');
    }
};

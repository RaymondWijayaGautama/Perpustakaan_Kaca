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
            $table->char('kode_mapel', 10)->unique('mst_mapel_pk');
            $table->integer('id_kurikulum')->nullable()->index('relation_223_fk');
            $table->integer('id_prog_keahlian')->nullable()->index('relation_228_fk');
            $table->integer('id_konsentrasi_keahlian')->nullable()->index('relation_234_fk');
            $table->char('kategori_kejuruan', 10)->nullable();
            $table->string('nama_mapel', 25)->nullable();
            $table->float('jp')->nullable();
            $table->float('total_jp')->nullable();
            $table->char('fase', 10)->nullable();
            $table->string('semester_mapel', 10)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['kode_mapel']);
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

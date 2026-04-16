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
        Schema::create('mst_pkg', function (Blueprint $table) {
            $table->integer('ID_MST_PKG')->unique('mst_pkg_pk');
            $table->string('NAMA_KOMPETENSI_PKG')->nullable();
            $table->string('JENIS_KOMPETENSI_PKG')->nullable();
            $table->float('BOBOT_KOMPETENSI_PKG')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_MST_PKG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_pkg');
    }
};

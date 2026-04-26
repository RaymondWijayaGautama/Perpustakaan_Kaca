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
        Schema::create('mst_kriteria_ketuntasan', function (Blueprint $table) {
            $table->integer('ID_KKTP', true)->unique('mst_kriteria_ketuntasan_pk');
            $table->integer('ID_ATP')->nullable()->index('relation_5979_fk');
            $table->integer('ID_MODUL_AJAR')->index('relation_6028_fk2');
            $table->string('INTERVAL_NILAI', 100)->nullable();
            $table->string('DESKRIPSI_INTERVAL')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_KKTP']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kriteria_ketuntasan');
    }
};

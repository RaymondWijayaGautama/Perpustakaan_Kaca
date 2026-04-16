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
        Schema::create('rapor_integritas', function (Blueprint $table) {
            $table->integer('id_r_integritas')->primary();
            $table->integer('id_ref_integritas')->nullable()->index('relation_1127_fk');
            $table->integer('id_siswa_tetap')->nullable()->index('relation_1129_fk');
            $table->double('nilai_integritas')->nullable();

            $table->unique(['id_r_integritas'], 'rapor_integritas_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_integritas');
    }
};

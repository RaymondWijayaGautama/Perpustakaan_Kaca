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
            $table->integer('ID_R_INTEGRITAS', true);
            $table->integer('ID_REF_INTEGRITAS')->nullable()->index('relation_1127_fk');
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_1129_fk');
            $table->float('NILAI_INTEGRITAS')->nullable();

            $table->unique(['ID_R_INTEGRITAS'], 'rapor_integritas_pk');
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

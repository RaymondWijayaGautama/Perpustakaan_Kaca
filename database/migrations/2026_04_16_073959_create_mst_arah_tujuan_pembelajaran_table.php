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
        Schema::create('mst_arah_tujuan_pembelajaran', function (Blueprint $table) {
            $table->integer('ID_ATP')->unique('mst_arah_tujuan_pembelajaran_p');
            $table->integer('ID_TUJUAN_PEMB')->nullable()->index('relation_4315_fk');
            $table->integer('ID_GURU_MAPEL')->nullable()->index('relation_6000_fk');
            $table->char('DESKRIPSI_ATP', 254)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_ATP']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_arah_tujuan_pembelajaran');
    }
};

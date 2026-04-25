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
        Schema::create('mst_kelas', function (Blueprint $table) {
            $table->integer('ID_KELAS', true)->unique('mst_kelas_pk');
            $table->integer('ID_TINGKAT')->nullable()->index('tingkat_kelas_fk');
            $table->char('KODE_KELAS', 3)->nullable();
            $table->boolean('IS_DELETE')->nullable();
            $table->integer('KUOTA_KELAS')->nullable();

            $table->primary(['ID_KELAS']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kelas');
    }
};

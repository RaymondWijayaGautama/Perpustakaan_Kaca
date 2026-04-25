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
        Schema::create('mst_unit', function (Blueprint $table) {
            $table->integer('ID_UNIT', true)->unique('mst_unit_pk');
            $table->string('NIP_KARYAWAN', 20)->index('relation_2400_fk');
            $table->char('KODE_UNIT', 10)->nullable();
            $table->char('NAMA_UNIT', 30)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_UNIT']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_unit');
    }
};

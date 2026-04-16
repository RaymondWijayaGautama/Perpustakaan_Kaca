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
            $table->integer('id_unit')->unique('mst_unit_pk');
            $table->string('nip_karyawan', 20)->index('relation_2400_fk');
            $table->char('kode_unit', 10)->nullable();
            $table->char('nama_unit', 30)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_unit']);
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

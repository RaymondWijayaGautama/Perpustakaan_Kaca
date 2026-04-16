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
        Schema::create('mst_coa', function (Blueprint $table) {
            $table->integer('id_master_coa')->unique('mst_coa_pk');
            $table->integer('mst_id_master_coa')->nullable()->index('relation_1251_fk');
            $table->char('kode_coa', 10)->nullable();
            $table->string('deskripsi_coa', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_master_coa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_coa');
    }
};

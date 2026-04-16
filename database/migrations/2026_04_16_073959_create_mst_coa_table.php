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
            $table->integer('ID_MASTER_COA')->unique('mst_coa_pk');
            $table->integer('MST_ID_MASTER_COA')->nullable()->index('relation_1251_fk');
            $table->char('KODE_COA', 10)->nullable();
            $table->string('DESKRIPSI_COA', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_MASTER_COA']);
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

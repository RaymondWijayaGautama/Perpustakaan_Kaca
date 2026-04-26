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
        Schema::create('dtl_fpd', function (Blueprint $table) {
            $table->integer('ID_DT_FPD', true)->unique('dtl_fpd_pk');
            $table->integer('ID_FPD')->nullable()->index('relation_1753_fk');
            $table->integer('ID_DT_PROGKER')->nullable()->index('relation_1761_fk');
            $table->integer('QTY')->nullable();
            $table->double('HARGA_SATUAN')->nullable();
            $table->integer('VOLUME')->nullable();
            $table->char('SATUAN', 10)->nullable();
            $table->double('TOTAL')->nullable();
            $table->string('LINK_BUKTI_NOTA_FPD')->nullable();

            $table->primary(['ID_DT_FPD']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtl_fpd');
    }
};

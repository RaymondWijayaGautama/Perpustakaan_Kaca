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
            $table->integer('id_dt_fpd')->unique('dtl_fpd_pk');
            $table->integer('id_fpd')->nullable()->index('relation_1753_fk');
            $table->integer('id_dt_progker')->nullable()->index('relation_1761_fk');
            $table->integer('qty')->nullable();
            $table->double('harga_satuan')->nullable();
            $table->integer('volume')->nullable();
            $table->char('satuan', 10)->nullable();
            $table->double('total')->nullable();
            $table->string('link_bukti_nota_fpd')->nullable();

            $table->primary(['id_dt_fpd']);
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

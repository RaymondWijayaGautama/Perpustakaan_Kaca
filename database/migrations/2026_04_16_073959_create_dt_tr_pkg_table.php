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
        Schema::create('dt_tr_pkg', function (Blueprint $table) {
            $table->integer('ID_DT_TR_PKG')->unique('dt_tr_pkg_pk');
            $table->integer('ID_TR_PKG')->nullable()->index('relation_2557_fk');
            $table->integer('ID_MST_PKG')->nullable()->index('relation_2558_fk');
            $table->char('NILAI_KOMPETENSI_PKG', 10)->nullable();
            $table->boolean('IS_VALID_PKG')->nullable();

            $table->primary(['ID_DT_TR_PKG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_tr_pkg');
    }
};

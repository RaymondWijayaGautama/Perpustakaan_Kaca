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
            $table->integer('id_dt_tr_pkg')->unique('dt_tr_pkg_pk');
            $table->integer('id_tr_pkg')->nullable()->index('relation_2557_fk');
            $table->integer('id_mst_pkg')->nullable()->index('relation_2558_fk');
            $table->char('nilai_kompetensi_pkg', 10)->nullable();
            $table->boolean('is_valid_pkg')->nullable();

            $table->primary(['id_dt_tr_pkg']);
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

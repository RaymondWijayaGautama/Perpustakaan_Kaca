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
        Schema::create('mst_si_menu', function (Blueprint $table) {
            $table->integer('ID_SI_ROLE_MENU', true)->unique('mst_si_menu_pk');
            $table->integer('ID_SI')->nullable()->index('relation_15_fk');
            $table->string('NAMA_MENU', 25)->nullable();
            $table->string('DESKRIPSI_MENU', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_SI_ROLE_MENU']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_si_menu');
    }
};

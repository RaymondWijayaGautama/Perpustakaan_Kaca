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
        Schema::create('jabatan_menu', function (Blueprint $table) {
            $table->integer('ID_HAK_AKSES', true)->unique('jabatan_menu_pk');
            $table->integer('ID_SI_ROLE_MENU')->nullable()->index('relation_8842_fk');
            $table->integer('ID_JABATAN')->nullable()->index('relation_8843_fk');

            $table->primary(['ID_HAK_AKSES']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_menu');
    }
};

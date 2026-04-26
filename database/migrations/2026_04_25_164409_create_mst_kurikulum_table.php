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
        Schema::create('mst_kurikulum', function (Blueprint $table) {
            $table->integer('ID_KURIKULUM', true)->unique('mst_kurikulum_pk');
            $table->string('NAMA_KURIKULUM', 100)->nullable();
            $table->string('NO_SK_PENETAPAN', 100)->nullable();
            $table->string('STATUS_KURIKULUM', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_KURIKULUM']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kurikulum');
    }
};

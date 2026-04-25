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
        Schema::create('access_log', function (Blueprint $table) {
            $table->integer('ID_ACCESS_LOG', true)->unique('access_log_pk');
            $table->dateTime('START_LOGIN')->nullable();
            $table->dateTime('END_LOGIN')->nullable();
            $table->string('USERNAME', 25)->nullable();
            $table->string('ROLE', 10)->nullable();

            $table->primary(['ID_ACCESS_LOG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_log');
    }
};

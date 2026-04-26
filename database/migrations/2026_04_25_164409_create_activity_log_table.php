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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->integer('ID_ACTIVITY_LOG', true)->unique('activity_log_pk');
            $table->integer('ID_ACCESS_LOG')->nullable()->index('relation_6979_fk');
            $table->dateTime('EVENT_TIME')->nullable();
            $table->string('ACTOR_USERNAME', 25)->nullable();
            $table->string('ACTOR_ROLE', 100)->nullable();
            $table->string('ACTIVITY_NAME', 100)->nullable();
            $table->string('RELATED_DATA', 100)->nullable();
            $table->string('ACTIVITY_DESCRIPTION')->nullable();

            $table->primary(['ID_ACTIVITY_LOG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};

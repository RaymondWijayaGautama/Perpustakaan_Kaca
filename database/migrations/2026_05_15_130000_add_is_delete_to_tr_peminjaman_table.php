<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tr_peminjaman', 'IS_DELETE')) {
            return;
        }

        Schema::table('tr_peminjaman', function (Blueprint $table) {
            $table->boolean('IS_DELETE')->default(false)->after('DENDA_PEMINJAMAN');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('tr_peminjaman', 'IS_DELETE')) {
            return;
        }

        Schema::table('tr_peminjaman', function (Blueprint $table) {
            $table->dropColumn('IS_DELETE');
        });
    }
};

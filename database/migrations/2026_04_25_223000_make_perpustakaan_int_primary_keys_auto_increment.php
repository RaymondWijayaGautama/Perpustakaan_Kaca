<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE cp_koleksi MODIFY ID_CP_KOLEKSI INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE mst_koleksi_laporan MODIFY ID_MST_LAPORAN INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE tr_peminjaman MODIFY ID_PEMINJAMAN INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE tr_kunjungan_perpus MODIFY ID_KUNJUNGAN INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE tr_pemusnahan_buku MODIFY ID_PEMUSNAHAN_BUKU INT(11) NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tr_pemusnahan_buku MODIFY ID_PEMUSNAHAN_BUKU INT(11) NOT NULL');
        DB::statement('ALTER TABLE tr_kunjungan_perpus MODIFY ID_KUNJUNGAN INT(11) NOT NULL');
        DB::statement('ALTER TABLE tr_peminjaman MODIFY ID_PEMINJAMAN INT(11) NOT NULL');
        DB::statement('ALTER TABLE mst_koleksi_laporan MODIFY ID_MST_LAPORAN INT(11) NOT NULL');
        DB::statement('ALTER TABLE cp_koleksi MODIFY ID_CP_KOLEKSI INT(11) NOT NULL');
    }
};

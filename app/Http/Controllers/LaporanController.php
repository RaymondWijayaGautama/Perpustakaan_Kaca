<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function siswaTerajin()
    {
        $siswaTerajin = Siswa::withCount('peminjaman')
            ->orderBy('peminjaman_count', 'desc')
            ->take(10)
            ->get();

        return view('laporan.siswa_terajin', compact('siswaTerajin'));
    }

    public function kunjunganBulanan()
    {
        
        $laporanKunjungan = \App\Models\Kunjungan::select(
                DB::raw('MONTHNAME(start_kunjungan) as bulan'),
                DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->groupBy('bulan', 'urutan_bulan')
            ->orderBy('urutan_bulan', 'asc')
            ->get();

        return view('laporan.kunjungan_bulanan', compact('laporanKunjungan'));
    }
}
<?php

namespace App\Services; // Sesuaikan dengan lokasi folder Anda

use Carbon\Carbon;

class KalkulasiKeterlambatanPengembalian
{
    /**
     * Menghitung status keterlambatan dan menentukan sanksi SP 1
     *
     * @param string $tglHarusKembali
     * @param string|null $tglKembali
     * @return array
     */
    public function hitung($tglHarusKembali, $tglKembali = null)
    {
        // Jika tgl_kembali kosong, asumsikan dikembalikan hari ini
        $tglKembaliParsed = $tglKembali ? Carbon::parse($tglKembali) : Carbon::now();
        $tglHarusKembaliParsed = Carbon::parse($tglHarusKembali);

        $hariTerlambat = 0;
        $sanksi = null;
        $keterangan = 'Dikembalikan tepat waktu';

        // Cek apakah tanggal kembali melewati tanggal jatuh tempo
        if ($tglKembaliParsed->greaterThan($tglHarusKembaliParsed)) {
            $hariTerlambat = $tglHarusKembaliParsed->diffInDays($tglKembaliParsed);
            
            // Berikan sanksi SP 1 berapapun hari keterlambatannya
            $sanksi = 'SP 1';
            $keterangan = "Terlambat {$hariTerlambat} hari. Diberikan sanksi: SP 1.";
        }

        return [
            'hari_terlambat' => $hariTerlambat,
            'sanksi'         => $sanksi,
            'keterangan'     => $keterangan,
            'tgl_kembali'    => $tglKembaliParsed->toDateString(),
        ];
    }
}
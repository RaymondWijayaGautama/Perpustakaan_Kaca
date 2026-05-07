<?php

namespace App\Http\Controllers\Pustakawan;

class KalkulasiDendaBukuRusak
{
    /**
     * Menghitung total denda kerusakan dan menentukan sanksi.
     * * @param float $nominalDenda  Input manual dari pustakawan
     * @param string $jenisKerusakan Contoh: "Sampul Robek", "Halaman Hilang"
     * @return array
     */
    public function hitung($nominalDenda, $jenisKerusakan)
    {
        // Pastikan nominal adalah angka
        $denda = (float) $nominalDenda;
        
        // Sesuai prosedur, kerusakan buku memicu sanksi administratif (SP 1)
        $sanksi = 'SP 1';
        
        // Format nominal ke Rupiah untuk keterangan
        $formatRupiah = "Rp " . number_format($denda, 0, ',', '.');

        if ($denda > 0) {
            $keterangan = "Buku rusak ({$jenisKerusakan}). Denda: {$formatRupiah} & Sanksi: {$sanksi}.";
        } else {
            $keterangan = "Buku dalam kondisi baik / Tidak ada denda kerusakan.";
            $sanksi = null;
        }

        return [
            'nominal_denda'   => $denda,
            'jenis_kerusakan' => $jenisKerusakan,
            'sanksi'          => $sanksi,
            'keterangan'      => $keterangan,
            'status_buku'     => $denda > 0 ? 'Rusak' : 'Baik'
        ];
    }
}
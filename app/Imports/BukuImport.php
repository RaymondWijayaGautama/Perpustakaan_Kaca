<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BukuImport implements ToCollection, WithStartRow
{
    public function __construct(private readonly string $nipKaryawan)
    {
    }

    public function startRow(): int
    {
        return 9;
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file_excel' => 'File Excel tidak terbaca atau kosong.',
            ]);
        }

        $kategoriMap = DB::table('ref_koleksi')
            ->where('IS_DELETE', 0)
            ->get(['ID_REF_KOLEKSI', 'NO_KATEGORI_BUKU'])
            ->mapWithKeys(fn($item) => [strtoupper(trim((string)$item->NO_KATEGORI_BUKU)) => $item->ID_REF_KOLEKSI])
            ->toArray();

        $seenIsbn = [];
        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $line = $index + 9;

            $judul = isset($row[6]) ? trim((string)$row[6]) : '';
            $isbn = isset($row[14]) ? trim((string)$row[14]) : '';

            if (empty($judul) && empty($isbn)) {
                continue;
            }

            $pengarang = isset($row[5]) ? trim((string)$row[5]) : '';
            $noKategoriExcel = isset($row[4]) ? strtoupper(trim((string)$row[4])) : ''; 
            $idKategoriInteger = $kategoriMap[$noKategoriExcel] ?? null;

            $penerbitMentah = isset($row[7]) ? trim((string)$row[7]) : '';
            
            // Ekstrak Tahun
            preg_match('/\b((?:19|20)\d{2})\b/', $penerbitMentah, $matches);
            $tahun = $matches[1] ?? date('Y');

            // Pecah string berdasarkan koma, ambil bagian pertama saja (Nama Penerbit)
            // Contoh: "Informatika, Bandung, 2023" -> Menjadi "Informatika"
            $penerbitArray = explode(',', $penerbitMentah);
            $penerbitFinal = trim($penerbitArray[0]);

            // Potong menjadi maksimal 50 karakter untuk mencegah error Data Too Long (sesuaikan jika perlu)
            if (strlen($penerbitFinal) > 50) {
                $penerbitFinal = substr($penerbitFinal, 0, 50);
            }
            // --------------------------

            $halamanMentah = isset($row[10]) ? (string)$row[10] : '0';
            $halaman = (int) preg_replace('/\D/', '', $halamanMentah);

            $eksemplar = isset($row[9]) ? (int)$row[9] : 1;
            $ukuran = isset($row[11]) ? trim((string)$row[11]) : '-';
            $keterangan = isset($row[15]) ? trim((string)$row[15]) : '';

            validator([
                'isbn' => $isbn,
                'judul' => $judul,
                'pengarang' => $pengarang,
                'id_kategori' => $idKategoriInteger,
            ], [
                'isbn' => ['required', 'max:25', Rule::unique('mst_koleksi_buku', 'ISBN')],
                'judul' => ['required', 'string', 'max:255'],
                'pengarang' => ['required', 'string', 'max:100'],
                'id_kategori' => ['required'], 
            ], [
                'judul.required' => "Judul pada baris {$line} (Kolom G) tidak terbaca. Pastikan kolom G terisi.",
                'id_kategori.required' => "Kategori '{$noKategoriExcel}' baris {$line} tidak ada di tabel ref_koleksi.",
            ])->validate();

            if (in_array($isbn, $seenIsbn, true)) {
                throw ValidationException::withMessages([
                    'file_excel' => "ISBN {$isbn} duplikat di dalam file pada baris {$line}.",
                ]);
            }

            $seenIsbn[] = $isbn;
            $preparedRows[] = [
                'ISBN' => $isbn,
                'ID_REF_KOLEKSI' => $idKategoriInteger,
                'JUDUL_KOLEKSI' => $judul,
                'PENGARANG' => $pengarang,
                'PENERBIT' => $penerbitFinal, 
                'TAHUN' => $tahun,
                'JUMLAH_EKSEMPLAR' => $eksemplar > 0 ? $eksemplar : 1,
                'JUMLAH_HALAMAN' => $halaman,
                'UKURAN_BUKU' => $ukuran,
                'KETERANGAN_BUKU' => $keterangan ?: 'Import Excel',
            ];
        }

        DB::transaction(function () use ($preparedRows) {
            $lastNb = DB::table('mst_koleksi_buku')->max('NB_KOLEKSI');
            $nextNb = ($lastNb ? (int) $lastNb : 0) + 1;

            foreach ($preparedRows as $row) {
                DB::table('mst_koleksi_buku')->insert([
                    'NB_KOLEKSI' => $nextNb++,
                    'ISBN' => $row['ISBN'],
                    'ID_REF_KOLEKSI' => $row['ID_REF_KOLEKSI'],
                    'JUDUL_KOLEKSI' => $row['JUDUL_KOLEKSI'],
                    'PENGARANG' => $row['PENGARANG'],
                    'PENERBIT' => $row['PENERBIT'],
                    'TAHUN' => $row['TAHUN'],
                    'TGL_MASUK_KOLEKSI' => now(),
                    'JUMLAH_EKSEMPLAR' => $row['JUMLAH_EKSEMPLAR'],
                    'JUMLAH_HALAMAN' => $row['JUMLAH_HALAMAN'],
                    'UKURAN_BUKU' => $row['UKURAN_BUKU'],
                    'KETERANGAN_BUKU' => $row['KETERANGAN_BUKU'],
                    'NO_RAK_BUKU' => 'Belum diatur',
                    'IS_DELETE' => 0,
                ]);

                for ($i = 0; $i < $row['JUMLAH_EKSEMPLAR']; $i++) {
                    DB::table('cp_koleksi')->insert([
                        'ISBN' => $row['ISBN'],
                        'STATUS_BUKU' => 'Tersedia',
                    ]);
                }
            }
        });
    }
}
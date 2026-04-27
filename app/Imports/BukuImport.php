<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BukuImport implements ToCollection, WithHeadingRow
{
    public function __construct(private readonly string $nipKaryawan)
    {
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file_excel' => 'File Excel kosong.',
            ]);
        }

        $seenIsbn = [];
        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $isbn = preg_replace('/\D/', '', trim((string) ($row['isbn'] ?? '')));
            $judul = trim((string) ($row['judul'] ?? ''));
            $pengarang = trim((string) ($row['pengarang'] ?? ''));
            $tahun = trim((string) ($row['tahun'] ?? ''));
            $kategori = (int) ($row['id_kategori'] ?? 0);

            validator([
                'isbn' => $isbn,
                'judul' => $judul,
                'pengarang' => $pengarang,
                'tahun' => $tahun,
                'id_kategori' => $kategori,
            ], [
                'isbn' => [
                    'required',
                    'max:25',
                    Rule::unique('mst_koleksi_buku', 'ISBN'),
                    function (string $attribute, mixed $value, \Closure $fail) {
                        $normalized = preg_replace('/\D/', '', (string) $value);

                        if (strlen($normalized) !== 13) {
                            $fail('ISBN harus terdiri dari 13 digit angka. Contoh: 978-602-8519-93-9.');
                            return;
                        }

                        if (!str_starts_with($normalized, '978') && !str_starts_with($normalized, '979')) {
                            $fail('ISBN harus diawali 978 atau 979.');
                        }
                    },
                ],
                'judul' => ['required', 'string', 'max:255'],
                'pengarang' => ['required', 'string', 'max:100'],
                'tahun' => ['required', 'digits:4'],
                'id_kategori' => [
                    'required',
                    Rule::exists('ref_koleksi', 'ID_REF_KOLEKSI')->where('IS_DELETE', 0),
                    Rule::notIn([4]),
                ],
            ], [], [
                'isbn' => "ISBN baris {$line}",
                'judul' => "Judul baris {$line}",
                'pengarang' => "Pengarang baris {$line}",
                'tahun' => "Tahun baris {$line}",
                'id_kategori' => "Kategori baris {$line}",
            ])->validate();

            if (in_array($isbn, $seenIsbn, true)) {
                throw ValidationException::withMessages([
                    'file_excel' => "ISBN {$isbn} duplikat pada file impor.",
                ]);
            }

            $seenIsbn[] = $isbn;
            $preparedRows[] = [
                'ISBN' => $isbn,
                'ID_REF_KOLEKSI' => $kategori,
                'JUDUL_KOLEKSI' => $judul,
                'PENGARANG' => $pengarang,
                'PENERBIT' => 'Belum diatur',
                'TAHUN' => $tahun,
                'KETERANGAN_BUKU' => 'Buku baru dari import Excel',
                'NO_RAK_BUKU' => 'Belum diatur',
            ];
        }

        DB::transaction(function () use ($preparedRows) {
            $nextNb = ((int) DB::table('mst_koleksi_buku')->max('NB_KOLEKSI')) + 1;

            foreach ($preparedRows as $row) {
                DB::table('mst_koleksi_buku')->insert([
                    'ISBN' => $row['ISBN'],
                    'ID_REF_KOLEKSI' => $row['ID_REF_KOLEKSI'],
                    'JUDUL_KOLEKSI' => $row['JUDUL_KOLEKSI'],
                    'PENGARANG' => $row['PENGARANG'],
                    'PENERBIT' => $row['PENERBIT'],
                    'TAHUN' => $row['TAHUN'],
                    'NB_KOLEKSI' => $nextNb++,
                    'TGL_MASUK_KOLEKSI' => now(),
                    'JUMLAH_EKSEMPLAR' => 1, 
                    'JUMLAH_HALAMAN' => 0,
                    'UKURAN_BUKU' => '-',
                    'BIBLIOGRAFI' => '-',
                    'INDEKS_AWAL_AKHIR' => 0,
                    'KETERANGAN_BUKU' => $row['KETERANGAN_BUKU'],
                    'NO_RAK_BUKU' => $row['NO_RAK_BUKU'],
                    'IS_DELETE' => 0,
                ]);

                DB::table('cp_koleksi')->insert([
                    'ISBN' => $row['ISBN'],
                    'ID_MST_LAPORAN' => null,
                    'STATUS_BUKU' => 'Tersedia',
                ]);
            }
        });
    }
}
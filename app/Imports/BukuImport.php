<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class BukuImport implements ToCollection, WithHeadingRow
{
    public function __construct(private readonly string $nipKaryawan)
    {
    }

    private function value(Collection $row, array $keys, mixed $default = ''): mixed
    {
        foreach ($keys as $key) {
            $value = $row->get($key);

            if ($value !== null && trim((string) $value) !== '') {
                return $value;
            }
        }

        return $default;
    }

    private function resolveKategori(mixed $value): int
    {
        $kategori = trim((string) $value);

        if ($kategori === '') {
            return 0;
        }

        $query = DB::table('ref_koleksi')->where('IS_DELETE', 0);

        if (ctype_digit($kategori)) {
            $byId = (clone $query)
                ->where('ID_REF_KOLEKSI', (int) $kategori)
                ->value('ID_REF_KOLEKSI');

            if ($byId !== null) {
                return (int) $byId;
            }

            $byNo = (clone $query)
                ->where('NO_KATEGORI_BUKU', $kategori)
                ->value('ID_REF_KOLEKSI');

            return $byNo === null ? 0 : (int) $byNo;
        }

        $id = $query
            ->where('DESKRIPSI_KATEGORI', 'like', $kategori)
            ->value('ID_REF_KOLEKSI');

        return $id === null ? 0 : (int) $id;
    }

    private function numberValue(mixed $value, int $default = 0): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (preg_match('/\d+/', (string) $value, $matches)) {
            return (int) $matches[0];
        }

        return $default;
    }

    private function dateValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value);
        }

        $tanggal = trim((string) $value);

        if ($tanggal === '') {
            return now();
        }

        try {
            return Carbon::parse($tanggal);
        } catch (\Throwable) {
            return now();
        }
    }

    private function publicationInfo(Collection $row): array
    {
        $combined = trim((string) $this->value($row, ['penerbit_kota_tahun_cet', 'penerbit_kota_tahun_cetakan']));
        $penerbit = trim((string) $this->value($row, ['penerbit']));
        $tahun = trim((string) $this->value($row, ['tahun']));

        if ($combined !== '') {
            if ($tahun === '' && preg_match('/\b((?:19|20)\d{2})\b/', $combined, $matches)) {
                $tahun = $matches[1];
            }

            if ($penerbit === '') {
                $parts = $tahun === ''
                    ? [$combined]
                    : preg_split('/\b' . preg_quote($tahun, '/') . '\b/', $combined);

                $penerbit = trim((string) ($parts[0] ?? $combined), " \t\n\r\0\x0B,.-");
            }
        }

        return [
            $penerbit === '' ? 'Belum diatur' : $penerbit,
            $tahun,
        ];
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
            $row = collect($row);

            if ($row->filter(fn ($value) => trim((string) $value) !== '')->isEmpty()) {
                continue;
            }

            $isbn = preg_replace('/\D/', '', trim((string) $this->value($row, ['isbn'])));

            if ($isbn === '' && $line <= 3) {
                continue;
            }

            [$penerbit, $tahun] = $this->publicationInfo($row);

            $judul = trim((string) $this->value($row, ['judul_buku', 'judul']));
            $pengarang = trim((string) $this->value($row, ['pengarang', 'penulis']));
            $kategori = $this->resolveKategori($this->value($row, ['kategori', 'id_kategori', 'no_kategori_buku', 'no_kode']));
            $rak = trim((string) $this->value($row, ['rak', 'no_rak_buku', 'nomor_rak'], '-'));
            $jumlahEksemplar = (int) $this->value($row, ['jumlah_eksemplar', 'eksemplar', 'jumlah'], 1);
            $noInduk = $this->numberValue($this->value($row, ['no_induk', 'nb_koleksi']), 0);
            $tanggalMasuk = $this->dateValue($this->value($row, ['tanggal_diterima', 'tgl_masuk_koleksi', 'tgl_masuk']));
            $jumlahHalaman = $this->numberValue($this->value($row, ['jumlah_halaman_romawi_angka', 'jumlah_halaman']), 0);
            $ukuranBuku = trim((string) $this->value($row, ['ukuran_buku', 'tinggi'], '-'));
            $bibliografi = trim((string) $this->value($row, ['bibliografi'], '-'));
            $indeks = $this->numberValue($this->value($row, ['indeks_awal_akhir', 'indeks']), 0);
            $keterangan = trim((string) $this->value($row, ['keterangan', 'keterangan_buku'], 'Buku baru dari import Excel'));

            validator([
                'isbn' => $isbn,
                'judul' => $judul,
                'pengarang' => $pengarang,
                'penerbit' => $penerbit,
                'tahun' => $tahun,
                'id_kategori' => $kategori,
                'rak' => $rak,
                'jumlah_eksemplar' => $jumlahEksemplar,
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
                'penerbit' => ['required', 'string', 'max:100'],
                'tahun' => ['required', 'digits:4'],
                'id_kategori' => [
                    'required',
                    Rule::exists('ref_koleksi', 'ID_REF_KOLEKSI')->where('IS_DELETE', 0),
                    function (string $attribute, mixed $value, \Closure $fail) {
                        $isLaporanPkl = DB::table('ref_koleksi')
                            ->where('ID_REF_KOLEKSI', (int) $value)
                            ->where('NO_KATEGORI_BUKU', '4')
                            ->where('IS_DELETE', 0)
                            ->exists();

                        if ($isLaporanPkl) {
                            $fail('Kategori laporan PKL hanya dapat diimpor melalui panel Laporan PKL.');
                        }
                    },
                ],
                'rak' => ['required', 'string', 'max:100'],
                'jumlah_eksemplar' => ['required', 'integer', 'min:1', 'max:1000'],
            ], [], [
                'isbn' => "ISBN baris {$line}",
                'judul' => "Judul baris {$line}",
                'pengarang' => "Pengarang baris {$line}",
                'penerbit' => "Penerbit baris {$line}",
                'tahun' => "Tahun baris {$line}",
                'id_kategori' => "Kategori baris {$line}",
                'rak' => "Rak baris {$line}",
                'jumlah_eksemplar' => "Jumlah Eksemplar baris {$line}",
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
                'PENERBIT' => $penerbit,
                'TAHUN' => $tahun,
                'NO_RAK_BUKU' => $rak,
                'JUMLAH_EKSEMPLAR' => $jumlahEksemplar,
                'NB_KOLEKSI' => $noInduk,
                'TGL_MASUK_KOLEKSI' => $tanggalMasuk,
                'JUMLAH_HALAMAN' => $jumlahHalaman,
                'UKURAN_BUKU' => $ukuranBuku === '' ? '-' : $ukuranBuku,
                'BIBLIOGRAFI' => $bibliografi === '' ? '-' : $bibliografi,
                'INDEKS_AWAL_AKHIR' => $indeks,
                'KETERANGAN_BUKU' => $keterangan === '' ? 'Buku baru dari import Excel' : $keterangan,
            ];
        }

        if ($preparedRows === []) {
            throw ValidationException::withMessages([
                'file_excel' => 'File Excel tidak memiliki baris data. Isi data mulai baris keempat sesuai template impor buku.',
            ]);
        }

        DB::transaction(function () use ($preparedRows) {
            $nextNb = ((int) DB::table('mst_koleksi_buku')->max('NB_KOLEKSI')) + 1;

            foreach ($preparedRows as $row) {
                $nbKoleksi = $row['NB_KOLEKSI'] > 0 ? $row['NB_KOLEKSI'] : $nextNb++;

                if ($nbKoleksi >= $nextNb) {
                    $nextNb = $nbKoleksi + 1;
                }

                DB::table('mst_koleksi_buku')->insert([
                    'ISBN' => $row['ISBN'],
                    'ID_REF_KOLEKSI' => $row['ID_REF_KOLEKSI'],
                    'JUDUL_KOLEKSI' => $row['JUDUL_KOLEKSI'],
                    'PENGARANG' => $row['PENGARANG'],
                    'PENERBIT' => $row['PENERBIT'],
                    'TAHUN' => $row['TAHUN'],
                    'NB_KOLEKSI' => $nbKoleksi,
                    'TGL_MASUK_KOLEKSI' => $row['TGL_MASUK_KOLEKSI'],
                    'JUMLAH_EKSEMPLAR' => $row['JUMLAH_EKSEMPLAR'], 
                    'JUMLAH_HALAMAN' => $row['JUMLAH_HALAMAN'],
                    'UKURAN_BUKU' => $row['UKURAN_BUKU'],
                    'BIBLIOGRAFI' => $row['BIBLIOGRAFI'],
                    'INDEKS_AWAL_AKHIR' => $row['INDEKS_AWAL_AKHIR'],
                    'KETERANGAN_BUKU' => $row['KETERANGAN_BUKU'],
                    'NO_RAK_BUKU' => $row['NO_RAK_BUKU'],
                    'IS_DELETE' => 0,
                ]);

                $copies = [];

                for ($copy = 0; $copy < $row['JUMLAH_EKSEMPLAR']; $copy++) {
                    $copies[] = [
                        'ISBN' => $row['ISBN'],
                        'ID_MST_LAPORAN' => null,
                        'STATUS_BUKU' => 'Tersedia',
                    ];
                }

                DB::table('cp_koleksi')->insert($copies);
            }
        });
    }
}

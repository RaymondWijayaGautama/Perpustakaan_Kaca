<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\BukuImport;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('mst_koleksi_buku as buku')
            ->leftJoin('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
            ->where('buku.IS_DELETE', 0)
            ->where(function ($query) {
                $query->where('kategori.NO_KATEGORI_BUKU', '!=', '4')
                    ->orWhereNull('kategori.NO_KATEGORI_BUKU');
            })
            ->select(
                'buku.ISBN as ISBN',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'buku.JUMLAH_EKSEMPLAR as jumlah_eksemplar',
                'kategori.DESKRIPSI_KATEGORI as kategori',
                'buku.ID_REF_KOLEKSI as id_ref_koleksi'
            );

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                  ->orWhere('buku.PENGARANG', 'like', "%{$search}%")
                  ->orWhere('buku.ISBN', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('buku.ID_REF_KOLEKSI', $request->kategori);
        }

        // Return pagination untuk React
        return response()->json($query->paginate($request->input('per_page', 10)));
    }

    public function getKategori()
    {
        $kategori = DB::table('ref_koleksi')
            ->where('IS_DELETE', 0)
            ->where(function ($query) {
                $query->where('NO_KATEGORI_BUKU', '!=', '4')
                    ->orWhereNull('NO_KATEGORI_BUKU');
            })
            ->select('ID_REF_KOLEKSI as id_ref_koleksi', 'DESKRIPSI_KATEGORI as deskripsi')
            ->get();
            
        return response()->json($kategori);
    }

    private function importRowValue(array $row, array $keys, mixed $default = ''): mixed
    {
        foreach ($keys as $key) {
            $value = $row[$key] ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                return $value;
            }
        }

        return $default;
    }

    private function importNumberValue(mixed $value, int $default = 0): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (preg_match('/\d+/', (string) $value, $matches)) {
            return (int) $matches[0];
        }

        return $default;
    }

    private function importDateValue(mixed $value): mixed
    {
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

    private function importPublicationInfo(array $row): array
    {
        $combined = trim((string) $this->importRowValue($row, ['penerbit_kota_tahun_cet', 'penerbit_kota_tahun_cetakan']));
        $penerbit = trim((string) $this->importRowValue($row, ['penerbit']));
        $tahun = trim((string) $this->importRowValue($row, ['tahun']));

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

    private function importCsvFile(string $filePath, string $nipKaryawan): void
    {
        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \RuntimeException('File CSV tidak dapat dibaca.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw ValidationException::withMessages([
                'file_excel' => 'File CSV kosong atau tidak memiliki header.',
            ]);
        }

        $normalizedHeaders = array_map(static function ($header) {
            $normalized = strtolower(trim((string) $header));
            $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized) ?? $normalized;
            return trim($normalized, '_');
        }, $headers);

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            if (count(array_filter($data, static fn($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = [];
            foreach ($normalizedHeaders as $index => $header) {
                $row[$header] = trim((string) ($data[$index] ?? ''));
            }
            $rows[] = $row;
        }

        fclose($handle);

        if (count($rows) === 0) {
            throw ValidationException::withMessages([
                'file_excel' => 'File CSV tidak memiliki data isi.',
            ]);
        }

        $seenIsbn = [];
        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $isbn = preg_replace('/\D/', '', trim((string) $this->importRowValue($row, ['isbn'])));

            if ($isbn === '' && $line <= 3) {
                continue;
            }

            [$penerbit, $tahun] = $this->importPublicationInfo($row);

            $judul = trim((string) $this->importRowValue($row, ['judul_buku', 'judul']));
            $pengarang = trim((string) $this->importRowValue($row, ['pengarang', 'penulis']));
            $kategori = $this->resolveImportKategori($this->importRowValue($row, ['kategori', 'id_kategori', 'no_kategori_buku', 'no_kode']));
            $rak = trim((string) $this->importRowValue($row, ['rak', 'no_rak_buku', 'nomor_rak'], '-'));
            $jumlahEksemplar = (int) $this->importRowValue($row, ['jumlah_eksemplar', 'eksemplar', 'jumlah'], 1);
            $noInduk = $this->importNumberValue($this->importRowValue($row, ['no_induk', 'nb_koleksi']), 0);
            $tanggalMasuk = $this->importDateValue($this->importRowValue($row, ['tanggal_diterima', 'tgl_masuk_koleksi', 'tgl_masuk']));
            $jumlahHalaman = $this->importNumberValue($this->importRowValue($row, ['jumlah_halaman_romawi_angka', 'jumlah_halaman']), 0);
            $ukuranBuku = trim((string) $this->importRowValue($row, ['ukuran_buku', 'tinggi'], '-'));
            $bibliografi = trim((string) $this->importRowValue($row, ['bibliografi'], '-'));
            $indeks = $this->importNumberValue($this->importRowValue($row, ['indeks_awal_akhir', 'indeks']), 0);
            $keterangan = trim((string) $this->importRowValue($row, ['keterangan', 'keterangan_buku'], 'Buku baru dari import CSV'));

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
                'KETERANGAN_BUKU' => $keterangan === '' ? 'Buku baru dari import CSV' : $keterangan,
            ];
        }

        if ($preparedRows === []) {
            throw ValidationException::withMessages([
                'file_excel' => 'File CSV tidak memiliki baris data. Isi data mulai baris keempat sesuai template impor buku.',
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
                    'JUMLAH_EKSEMPLAR' => $row['JUMLAH_EKSEMPLAR'], // Diperbaiki: eksemplar (Sesuai SQL)
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

    private function resolveImportKategori(mixed $value): int
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

    private function getPustakawan(?string $nipKaryawan): ?object
    {
        if (!$nipKaryawan) {
            return null;
        }

        return DB::table('mst_karyawan')
            ->where('NIP_KARYAWAN', $nipKaryawan)
            ->where('IS_DELETE', 0)
            ->first();
    }

    private function ensurePustakawan(?string $nipKaryawan): void
    {
        $petugas = $this->getPustakawan($nipKaryawan);

        if (!$petugas || strtolower((string) $petugas->JABATAN_FUNGSIONAL) !== 'pustakawan') {
            abort(403, 'Hanya pustakawan yang dapat mengakses fitur ini.');
        }
    }

    public function importExcel(Request $request)
    {
        $nipKaryawan = $request->input('nip_karyawan', $request->query('nip', 'SYSTEM'));
        // $this->ensurePustakawan($nipKaryawan);

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
            // 'nip_karyawan' => 'required|string|max:20',
        ]);

        try {
            $uploadedFile = $request->file('file_excel');
            $extension = strtolower((string) $uploadedFile->getClientOriginalExtension());

            if ($extension === 'csv') {
                $this->importCsvFile($uploadedFile->getRealPath(), $nipKaryawan);
            } else {
                if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                    return redirect()->back()->with('error', 'Import .xlsx/.xls belum aktif di server ini. Aktifkan ekstensi PHP gd dan zip lalu pasang paket maatwebsite/excel.');
                }
                \Maatwebsite\Excel\Facades\Excel::import(new BukuImport($nipKaryawan), $uploadedFile);
            }

            return redirect()->back()->with('success', 'Data buku berhasil diimpor!');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function halamanImport(Request $request)
    {
        $nipKaryawan = $request->query('nip', 'SYSTEM');
        // $this->ensurePustakawan($nipKaryawan);

        return view('bukuimport', [
            'nipKaryawan' => $nipKaryawan,
        ]);
    }

    public function downloadImportTemplate()
    {
        $path = resource_path('templates/Template Impor Buku.xlsx');

        abort_unless(is_file($path), 404);

        return response()->download($path, 'Template Impor Buku.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportExcel(Request $request)
{
    // $this->ensurePustakawan($request->query('nip'));

    $query = DB::table('mst_koleksi_buku as buku')
        ->leftJoin('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
        ->where('buku.IS_DELETE', 0)
        ->where(function ($query) {
            $query->where('kategori.NO_KATEGORI_BUKU', '!=', '4')
                ->orWhereNull('kategori.NO_KATEGORI_BUKU');
        })
        ->select(
            'buku.NB_KOLEKSI as no_induk',             
            'kategori.NO_KATEGORI_BUKU as no_kode',    
            'buku.PENGARANG as pengarang',             
            'buku.JUDUL_KOLEKSI as judul_koleksi',     
            'buku.PENERBIT as penerbit',               
            'buku.TAHUN as tahun',                     
            'buku.TGL_MASUK_KOLEKSI as tgl_diterima',  
            'buku.JUMLAH_EKSEMPLAR as jumlah_eksemplar',
            'buku.JUMLAH_HALAMAN as jumlah_halaman',
            'buku.UKURAN_BUKU as ukuran_buku',
            'buku.BIBLIOGRAFI as bibliografi',
            'buku.INDEKS_AWAL_AKHIR as indeks',
            'buku.ISBN as isbn',
            'buku.KETERANGAN_BUKU as keterangan'
        );

    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function ($subQuery) use ($search) {
            $subQuery->where('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                ->orWhere('buku.PENGARANG', 'like', "%{$search}%")
                ->orWhere('buku.ISBN', 'like', "%{$search}%");
        });
    }

    if ($request->filled('kategori')) {
        $query->where('buku.ID_REF_KOLEKSI', $request->kategori);
    }

    $dataBuku = $query
        ->orderBy('buku.JUDUL_KOLEKSI')
        ->get();

    $namaFile = 'Buku_Induk_Perpustakaan_' . date('Y-m-d_H-i-s') . '.xls';

    $html = view('pustakawan.buku.export_excel', [
        'dataBuku' => $dataBuku,
    ])->render();

    return response($html, 200, [
        'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
    ]);
}
}

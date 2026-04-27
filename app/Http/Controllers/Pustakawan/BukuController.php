<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
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
            ->select(
                'buku.ISBN as ISBN',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'buku.JUMLAH_EKSEMPLAR as jumlah_ekslempar',
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
            ->select('ID_REF_KOLEKSI as id_ref_koleksi', 'DESKRIPSI_KATEGORI as deskripsi')
            ->get();
            
        return response()->json($kategori);
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
            return strtolower(trim((string) $header));
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
                'KETERANGAN_BUKU' => 'Buku baru dari import CSV',
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
                    'JUMLAH_EKSEMPLAR' => 1, // Diperbaiki: EKSLEMPAR (Sesuai SQL)
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

    public function exportExcel(Request $request)
    {
        $this->ensurePustakawan($request->query('nip'));

        $query = DB::table('mst_koleksi_buku as buku')
            ->leftJoin('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
            ->where('buku.IS_DELETE', 0)
            ->where('buku.ID_REF_KOLEKSI', '!=', 4)
            ->select(
                'buku.ISBN',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'buku.JUMLAH_EKSEMPLAR as jumlah_ekslempar', // Diperbaiki: EKSLEMPAR
                'kategori.DESKRIPSI_KATEGORI as kategori'
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

        $namaFile = 'Data_Buku_KACA_' . date('Y-m-d_H-i-s') . '.xls';

        $html = view('pustakawan.buku.export_excel', [
            'dataBuku' => $dataBuku,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
        ]);
    }
}
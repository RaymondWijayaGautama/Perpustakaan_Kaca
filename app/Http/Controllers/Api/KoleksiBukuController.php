<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class KoleksiBukuController extends Controller
{
    private function normalizeIsbn(string $value): string
    {
        return preg_replace('/\D/', '', trim($value));
    }

    private function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka.',
            'digits' => ':attribute harus terdiri dari :digits digit.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min.',
            'unique' => ':attribute sudah digunakan.',
            'exists' => ':attribute tidak valid.',
            'not_in' => ':attribute tidak valid.',
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'editor_nip_karyawan' => 'Identitas pustakawan',
            'ISBN' => 'ISBN',
            'judul_koleksi' => 'Judul buku',
            'pengarang' => 'Penulis',
            'penerbit' => 'Penerbit',
            'tahun' => 'Tahun',
            'jumlah_ekslempar' => 'Jumlah eksemplar',
            'no_rak_buku' => 'Nomor rak',
            'keterangan_buku' => 'Keterangan',
            'id_ref_koleksi' => 'Kategori',
            'isbn' => 'ISBN',
        ];
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

    private function ensurePustakawan(?string $nipKaryawan): ?JsonResponse
    {
        $petugas = $this->getPustakawan($nipKaryawan);

        if (!$petugas || strtolower((string) $petugas->JABATAN_FUNGSIONAL) !== 'pustakawan') {
            return response()->json([
                'message' => 'Hanya pustakawan yang dapat mengelola koleksi buku.',
            ], 403);
        }

        return null;
    }

    private function isbnRules(?string $ignoreIsbn = null): array
    {
        $uniqueRule = Rule::unique('mst_koleksi_buku', 'ISBN');

        if ($ignoreIsbn !== null) {
            $uniqueRule = $uniqueRule->ignore($ignoreIsbn, 'ISBN');
        }

        return [
            'required',
            'string',
            'max:25',
            $uniqueRule,
            function (string $attribute, mixed $value, \Closure $fail) {
                $normalized = $this->normalizeIsbn((string) $value);

                if (strlen($normalized) !== 13) {
                    $fail('ISBN harus terdiri dari 13 digit angka. Contoh: 978-602-8519-93-9.');
                    return;
                }

                if (!str_starts_with($normalized, '978') && !str_starts_with($normalized, '979')) {
                    $fail('ISBN harus diawali 978 atau 979.');
                }
            },
        ];
    }

    private function bookPayload(Request $request, ?string $ignoreIsbn = null): array
    {
        return validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'ISBN' => $this->isbnRules($ignoreIsbn),
            'judul_koleksi' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:100'],
            'penerbit' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'digits:4'],
            'jumlah_ekslempar' => ['required', 'integer', 'min:1'],
            'no_rak_buku' => ['required', 'string', 'max:100'],
            'keterangan_buku' => ['nullable', 'string', 'max:255'],
            'id_ref_koleksi' => [
                'required',
                'integer',
                Rule::exists('ref_koleksi', 'ID_REF_KOLEKSI')->where('IS_DELETE', 0),
                Rule::notIn([4]),
            ],
        ], array_merge($this->validationMessages(), [
            'id_ref_koleksi.not_in' => 'Kategori laporan PKL tidak dapat dipakai di menu koleksi buku.',
        ]), $this->validationAttributes())->validate();
    }

    private function nextNbKoleksi(): int
    {
        return ((int) DB::table('mst_koleksi_buku')->max('NB_KOLEKSI')) + 1;
    }

    private function syncCopyRows(string $isbn, int $targetQty): bool
    {
        $currentQty = (int) DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->count();

        if ($targetQty > $currentQty) {
            $rows = [];

            for ($index = 0; $index < ($targetQty - $currentQty); $index++) {
                $rows[] = [
                    'ISBN' => $isbn,
                    'ID_MST_LAPORAN' => null,
                    'STATUS_BUKU' => 'Tersedia',
                ];
            }

            if ($rows !== []) {
                DB::table('cp_koleksi')->insert($rows);
            }

            return true;
        }

        if ($targetQty < $currentQty) {
            $removeQty = $currentQty - $targetQty;

            $removableCopies = DB::table('cp_koleksi')
                ->where('ISBN', $isbn)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tr_peminjaman')
                        ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                        ->whereNull('tr_peminjaman.TGL_KEMBALI');
                })
                ->orderByDesc('ID_CP_KOLEKSI')
                ->limit($removeQty)
                ->pluck('ID_CP_KOLEKSI');

            if ($removableCopies->count() < $removeQty) {
                return false;
            }

            DB::table('cp_koleksi')
                ->whereIn('ID_CP_KOLEKSI', $removableCopies)
                ->delete();
        }

        return true;
    }

    private function fetchBook(string $isbn): ?object
    {
        return DB::table('mst_koleksi_buku as buku')
            ->join('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
            ->where('buku.ISBN', $isbn)
            ->select([
                'buku.ISBN',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.NB_KOLEKSI as nb_koleksi',
                'buku.TGL_MASUK_KOLEKSI as tgl_masuk_koleksi',
                'buku.JUMLAH_EKSEMPLAR as jumlah_ekslempar',
                'buku.JUMLAH_HALAMAN as jumlah_halaman',
                'buku.UKURAN_BUKU as ukuran_buku',
                'buku.BIBLIOGRAFI as bibliografi',
                'buku.INDEKS_AWAL_AKHIR as indeks_awal_akhir',
                'buku.KETERANGAN_BUKU as keterangan_buku',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'buku.ID_REF_KOLEKSI as id_ref_koleksi',
                'kategori.DESKRIPSI_KATEGORI as kategori',
            ])
            ->first();
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $judul = trim((string) $request->query('judul', ''));
        $penulis = trim((string) $request->query('penulis', ''));
        $kategori = $request->query('kategori');
        $sortBy = $request->query('sort_by', 'judul_koleksi');
        $sortOrder = strtolower((string) $request->query('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $allowedSort = [
            'judul_koleksi' => 'buku.JUDUL_KOLEKSI',
            'pengarang' => 'buku.PENGARANG',
            'tahun' => 'buku.TAHUN',
            'kategori' => 'kategori.DESKRIPSI_KATEGORI',
        ];

        $query = DB::table('mst_koleksi_buku as buku')
            ->join('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
            ->where('buku.IS_DELETE', 0)
            ->where('buku.ID_REF_KOLEKSI', '!=', 4)
            ->select([
                'buku.ISBN',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.NB_KOLEKSI as nb_koleksi',
                'buku.TGL_MASUK_KOLEKSI as tgl_masuk_koleksi',
                'buku.JUMLAH_EKSEMPLAR as jumlah_ekslempar',
                'buku.JUMLAH_HALAMAN as jumlah_halaman',
                'buku.UKURAN_BUKU as ukuran_buku',
                'buku.BIBLIOGRAFI as bibliografi',
                'buku.INDEKS_AWAL_AKHIR as indeks_awal_akhir',
                'buku.KETERANGAN_BUKU as keterangan_buku',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'buku.ID_REF_KOLEKSI as id_ref_koleksi',
                'kategori.DESKRIPSI_KATEGORI as kategori',
            ]);

        if ($search !== '') {
            $normalizedSearch = $this->normalizeIsbn($search);

            $query->where(function ($subQuery) use ($search, $normalizedSearch) {
                $subQuery->where('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                    ->orWhere('buku.PENGARANG', 'like', "%{$search}%")
                    ->orWhere('buku.ISBN', 'like', "%{$search}%");

                if ($normalizedSearch !== '' && $normalizedSearch !== $search) {
                    $subQuery->orWhere('buku.ISBN', 'like', "%{$normalizedSearch}%");
                }
            });
        }

        if ($judul !== '') {
            $query->where('buku.JUDUL_KOLEKSI', 'like', "%{$judul}%");
        }

        if ($penulis !== '') {
            $query->where('buku.PENGARANG', 'like', "%{$penulis}%");
        }

        if ($kategori !== null && $kategori !== '') {
            $query->where('buku.ID_REF_KOLEKSI', $kategori);
        }

        if ($sortBy === 'tahun') {
            return response()->json(
                $query->orderByRaw("CAST(buku.TAHUN AS UNSIGNED) {$sortOrder}")->paginate($perPage)
            );
        }

        return response()->json(
            $query->orderBy($allowedSort[$sortBy] ?? $allowedSort['judul_koleksi'], $sortOrder)->paginate($perPage)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->bookPayload($request);

        if ($authError = $this->ensurePustakawan($payload['editor_nip_karyawan'])) {
            return $authError;
        }

        DB::transaction(function () use ($payload) {
            $normalizedIsbn = $this->normalizeIsbn((string) $payload['ISBN']);

            DB::table('mst_koleksi_buku')->insert([
                'ISBN' => $normalizedIsbn,
                'ID_REF_KOLEKSI' => $payload['id_ref_koleksi'],
                'JUDUL_KOLEKSI' => trim((string) $payload['judul_koleksi']),
                'PENGARANG' => trim((string) $payload['pengarang']),
                'PENERBIT' => trim((string) $payload['penerbit']),
                'TAHUN' => trim((string) $payload['tahun']),
                'NB_KOLEKSI' => $this->nextNbKoleksi(),
                'TGL_MASUK_KOLEKSI' => now(),
                'JUMLAH_EKSEMPLAR' => $payload['jumlah_ekslempar'],
                'JUMLAH_HALAMAN' => 0,
                'UKURAN_BUKU' => '-',
                'BIBLIOGRAFI' => '-',
                'INDEKS_AWAL_AKHIR' => 0,
                'KETERANGAN_BUKU' => trim((string) ($payload['keterangan_buku'] ?? '')),
                'NO_RAK_BUKU' => trim((string) $payload['no_rak_buku']),
                'IS_DELETE' => 0,
            ]);

            $this->syncCopyRows($normalizedIsbn, (int) $payload['jumlah_ekslempar']);
        });

        $book = $this->fetchBook($this->normalizeIsbn((string) $payload['ISBN']));

        return response()->json([
            'message' => 'Koleksi buku berhasil ditambahkan.',
            'data' => $book,
        ], 201);
    }

    public function update(Request $request, string $isbn): JsonResponse
    {
        $payload = $this->bookPayload($request, $isbn);

        if ($authError = $this->ensurePustakawan($payload['editor_nip_karyawan'])) {
            return $authError;
        }

        $existing = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('IS_DELETE', 0)
            ->first();

        if (!$existing) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        $normalizedIsbn = $this->normalizeIsbn((string) $payload['ISBN']);
        try {
            DB::transaction(function () use ($isbn, $payload, $normalizedIsbn) {
                DB::table('mst_koleksi_buku')
                    ->where('ISBN', $isbn)
                    ->update([
                        'ISBN' => $normalizedIsbn,
                        'ID_REF_KOLEKSI' => $payload['id_ref_koleksi'],
                        'JUDUL_KOLEKSI' => trim((string) $payload['judul_koleksi']),
                        'PENGARANG' => trim((string) $payload['pengarang']),
                        'PENERBIT' => trim((string) $payload['penerbit']),
                        'TAHUN' => trim((string) $payload['tahun']),
                        'JUMLAH_EKSEMPLAR' => $payload['jumlah_ekslempar'],
                        'KETERANGAN_BUKU' => trim((string) ($payload['keterangan_buku'] ?? '')),
                        'NO_RAK_BUKU' => trim((string) $payload['no_rak_buku']),
                    ]);

                if ($normalizedIsbn !== $isbn) {
                    DB::table('cp_koleksi')
                        ->where('ISBN', $isbn)
                        ->update(['ISBN' => $normalizedIsbn]);
                }

                if (!$this->syncCopyRows($normalizedIsbn, (int) $payload['jumlah_ekslempar'])) {
                    throw new \RuntimeException('__COPY_SYNC_FAILED__');
                }
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() !== '__COPY_SYNC_FAILED__') {
                throw $exception;
            }

            return response()->json([
                'message' => 'Jumlah eksemplar tidak bisa dikurangi karena masih ada copy aktif atau sedang dipinjam.',
            ], 409);
        }

        $book = $this->fetchBook($normalizedIsbn);

        return response()->json([
            'message' => 'Koleksi buku berhasil diperbarui.',
            'data' => $book,
        ]);
    }

    public function destroy(Request $request, string $isbn): JsonResponse
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
        ], $this->validationMessages(), $this->validationAttributes());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($authError = $this->ensurePustakawan($request->editor_nip_karyawan)) {
            return $authError;
        }

        $existing = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('IS_DELETE', 0)
            ->first();

        if (!$existing) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        $sedangDipinjam = DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tr_peminjaman')
                    ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                    ->whereNull('tr_peminjaman.TGL_KEMBALI');
            })
            ->exists();

        if ($sedangDipinjam) {
            return response()->json([
                'message' => 'Koleksi buku tidak bisa dihapus karena sedang dipinjam.',
            ], 409);
        }

        $punyaRiwayatPeminjaman = DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tr_peminjaman')
                    ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI');
            })
            ->exists();

        try {
            DB::transaction(function () use ($isbn, $punyaRiwayatPeminjaman) {
                DB::table('mst_koleksi_buku')
                    ->where('ISBN', $isbn)
                    ->update(['IS_DELETE' => 1]);

                if ($punyaRiwayatPeminjaman) {
                    DB::table('cp_koleksi')
                        ->where('ISBN', $isbn)
                        ->update(['STATUS_BUKU' => 'Nonaktif']);

                    return;
                }

                DB::table('cp_koleksi')
                    ->where('ISBN', $isbn)
                    ->delete();
            });
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Koleksi buku gagal dihapus. Silakan coba lagi atau hubungi admin sistem.',
            ], 500);
        }

        return response()->json([
            'message' => $punyaRiwayatPeminjaman
                ? 'Koleksi buku berhasil dihapus dari daftar aktif. Copy fisik dipertahankan karena memiliki riwayat peminjaman.'
                : 'Koleksi buku berhasil dihapus.',
        ]);
    }

    public function generateBarcode(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'isbn' => ['required', 'string', 'max:25'],
        ], $this->validationMessages(), $this->validationAttributes());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($authError = $this->ensurePustakawan($request->editor_nip_karyawan)) {
            return $authError;
        }

        $isbn = $this->normalizeIsbn((string) $request->isbn);
        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('IS_DELETE', 0)
            ->first();

        if (!$buku) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        DB::transaction(function () use ($isbn, $buku) {
            $copyCount = (int) DB::table('cp_koleksi')->where('ISBN', $isbn)->count();

            if ($copyCount === 0) {
                $this->syncCopyRows($isbn, 1);
                return;
            }

            if ($copyCount < (int) $buku->JUMLAH_EKSEMPLAR) {
                $this->syncCopyRows($isbn, $copyCount + 1);
            }
        });

        $copy = DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->orderByDesc('ID_CP_KOLEKSI')
            ->first();

        $barcodeValue = $isbn . '/' . $copy->ID_CP_KOLEKSI;
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodePng = base64_encode(
                $generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128, 2, 60)
            );
            $barcodeHtml = "<img src='data:image/png;base64,{$barcodePng}' alt='Barcode {$barcodeValue}' style='display:block;max-width:100%;height:auto;' />";
        } catch (\Throwable $exception) {
            $generator = new BarcodeGeneratorSVG();
            $barcodeSvg = $generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128, 2, 60);
            $barcodeHtml = "<div style='display:flex;justify-content:center;width:100%;'>{$barcodeSvg}</div>";
        }

        $content = "
        <div style='width: 100%; display: flex; flex-direction: column; align-items: center;'>
            <div style='width: 100%; padding: 10px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px; font-weight: bold; font-size: 14px; margin-bottom: 24px; text-align: center;'>
                Barcode buku berhasil disiapkan
            </div>
            <div style='display: flex; justify-content: center; width: 100%; margin-bottom: 12px; background: white; padding: 10px;'>
                {$barcodeHtml}
            </div>
            <p style='font-family: monospace; letter-spacing: 2px; font-weight: bold; font-size: 15px; color: #1a1a1a; margin-top: 0; margin-bottom: 20px;'>
                {$barcodeValue}
            </p>
            <div style='margin-top: 10px; border-top: 1px dashed #d1d5db; width: 100%; padding-top: 15px; font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; text-align: center;'>
                Tersimpan pada copy fisik ID: <span style='color: #1f2937;'>{$copy->ID_CP_KOLEKSI}</span>
            </div>
        </div>
        ";

        return response()->json($content);
    }
}

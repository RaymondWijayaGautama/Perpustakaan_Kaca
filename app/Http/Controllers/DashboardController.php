<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon; // Tambahan untuk pengolahan tanggal
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    private function getPetugasPemusnahan(?string $nipKaryawan)
    {
        if (!$nipKaryawan) {
            return null;
        }

        return DB::table('mst_karyawan')
            ->where('nip_karyawan', $nipKaryawan)
            ->where('is_delete', 0)
            ->select(
                'NIP_KARYAWAN as nip_karyawan',
                'NAMA_KARYAWAN as nama_karyawan',
                'JABATAN_FUNGSIONAL as jabatan_fungsional'
            )
            ->first();
    }

    private function isPustakawan(?object $petugas): bool
    {
        return $petugas && strtolower((string) $petugas->jabatan_fungsional) === 'pustakawan';
    }

    private function getKategoriPemusnahan(object $buku, string $alasan): ?string
    {
        $metadata = strtolower(trim(($buku->keterangan_buku ?? '') . ' ' . $alasan));

        if (str_contains($metadata, 'rusak')) {
            return 'Rusak';
        }

        if (
            str_contains($metadata, 'non-aktif') ||
            str_contains($metadata, 'nonaktif') ||
            str_contains($metadata, 'hilang') ||
            str_contains($metadata, 'tidak dikembalikan')
        ) {
            return 'Non-Aktif';
        }

        return null;
    }

    private function parsePemusnahanIdentifier(string $value): array
    {
        $raw = trim($value);
        $normalized = preg_replace('/\s+/', '', $raw) ?? '';

        if ($raw === '') {
            return [
                'raw' => '',
                'isbn' => null,
                'id_cp_koleksi' => null,
            ];
        }

        $separatorPatterns = [
            '/^(?<isbn>.+)[\/#_|](?<copy>\d+)$/',
            '/^(?<isbn>.+)-(?<copy>\d+)$/',
        ];

        foreach ($separatorPatterns as $pattern) {
            if (!preg_match($pattern, $normalized, $matches)) {
                continue;
            }

            $isbnDigits = preg_replace('/\D/', '', $matches['isbn']);

            if (preg_match('/^97[89]\d{10}$/', $isbnDigits)) {
                return [
                    'raw' => $raw,
                    'isbn' => $isbnDigits,
                    'id_cp_koleksi' => (int) $matches['copy'],
                ];
            }
        }

        $numeric = preg_replace('/\D/', '', $normalized);

        if (preg_match('/^97[89]\d{10}\d+$/', $numeric)) {
            return [
                'raw' => $raw,
                'isbn' => substr($numeric, 0, 13),
                'id_cp_koleksi' => (int) substr($numeric, 13),
            ];
        }

        if (preg_match('/^97[89]\d{10}$/', $numeric)) {
            return [
                'raw' => $raw,
                'isbn' => $numeric,
                'id_cp_koleksi' => null,
            ];
        }

        return [
            'raw' => $raw,
            'isbn' => null,
            'id_cp_koleksi' => ctype_digit($normalized) ? (int) $normalized : null,
        ];
    }

    private function legacyPemusnahanActiveScope($query)
    {
        return $query->where(function ($subQuery) {
            $subQuery->whereNull('pemusnahan.IS_DELETE')
                ->orWhere('pemusnahan.IS_DELETE', 0);
        });
    }

    private function legacyPemusnahanBaseQuery()
    {
        return $this->legacyPemusnahanActiveScope(
            DB::table('tr_pemusnahan_buku as pemusnahan')
                ->leftJoin('cp_koleksi as copy', 'pemusnahan.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                ->leftJoin('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->select(
                    'pemusnahan.ID_PEMUSNAHAN_BUKU as id',
                    'copy.ISBN as isbn',
                    'copy.ID_CP_KOLEKSI as id_cp_koleksi',
                    'pemusnahan.KET_PEMUSNAHAN_BUKU as alasan',
                    DB::raw('NULL as nip_karyawan'),
                    'pemusnahan.TGL_PEMUSNAHAN_BUKU as tanggal_pemusnahan',
                    DB::raw('pemusnahan.TGL_PEMUSNAHAN_BUKU as created_at'),
                    DB::raw('pemusnahan.TGL_PEMUSNAHAN_BUKU as updated_at'),
                    DB::raw("CASE WHEN copy.STATUS_BUKU = 'Dimusnahkan' THEN 'disetujui' ELSE 'menunggu_konfirmasi' END as status"),
                    'buku.judul_koleksi',
                    'buku.judul_koleksi as judul',
                    'buku.pengarang',
                    'buku.penerbit',
                    'buku.tahun',
                    'buku.nb_koleksi',
                    'buku.no_rak_buku',
                    'buku.keterangan_buku',
                    DB::raw('NULL as nama_petugas')
                )
        );
    }

    private function normalizeLegacyPemusnahanRow(?object $row): ?object
    {
        if (!$row) {
            return null;
        }

        $alasan = (string) ($row->alasan ?? '');

        if (preg_match('/^\[nip:([^\]]+)\]\s*/', $alasan, $matches)) {
            $row->nip_karyawan = $matches[1];
            $row->alasan = preg_replace('/^\[nip:[^\]]+\]\s*/', '', $alasan) ?? $alasan;

            $row->nama_petugas = DB::table('mst_karyawan')
                ->where('NIP_KARYAWAN', $row->nip_karyawan)
                ->where('IS_DELETE', 0)
                ->value('NAMA_KARYAWAN');
        }

        return $row;
    }

    private function legacyPemusnahanRow($id): ?object
    {
        return $this->normalizeLegacyPemusnahanRow(
            $this->legacyPemusnahanBaseQuery()
                ->where('pemusnahan.ID_PEMUSNAHAN_BUKU', $id)
                ->first()
        );
    }

    private function legacyEligibleCopy(array $identifier): ?object
    {
        $query = DB::table('cp_koleksi')
            ->where('ISBN', $identifier['isbn'])
            ->where(function ($subQuery) {
                $subQuery->whereNull('STATUS_BUKU')
                    ->orWhere('STATUS_BUKU', '!=', 'Dimusnahkan');
            })
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('tr_peminjaman')
                    ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                    ->whereNull('tr_peminjaman.TGL_KEMBALI')
                    ->whereIn('tr_peminjaman.STATUS_PEMINJAMAN', ['Dipinjam', 'Terlambat']);
            })
            ->select(
                'ID_CP_KOLEKSI as id_cp_koleksi',
                'ISBN',
                'STATUS_BUKU as status_buku'
            );

        if ($identifier['id_cp_koleksi']) {
            $query->where('ID_CP_KOLEKSI', $identifier['id_cp_koleksi']);
        }

        return $query->orderByDesc('ID_CP_KOLEKSI')->first();
    }

    private function legacyPemusnahanExistsForCopy(int $idCpKoleksi, ?int $ignoreId = null): bool
    {
        $query = DB::table('tr_pemusnahan_buku')
            ->where('ID_CP_KOLEKSI', $idCpKoleksi)
            ->where(function ($subQuery) {
                $subQuery->whereNull('IS_DELETE')
                    ->orWhere('IS_DELETE', 0);
            });

        if ($ignoreId !== null) {
            $query->where('ID_PEMUSNAHAN_BUKU', '!=', $ignoreId);
        }

        return $query->exists();
    }

    private function legacyPemusnahanDescription(string $nipKaryawan, string $kategoriPemusnahan, string $alasan): string
    {
        return substr('[nip:' . $nipKaryawan . '] [' . $kategoriPemusnahan . '] ' . trim($alasan), 0, 255);
    }

    private function updateLegacyPemusnahan(Request $request, $id, object $petugas)
    {
        $pemusnahan = $this->legacyPemusnahanRow($id);

        if (!$pemusnahan) {
            return response()->json(['message' => 'Data pemusnahan tidak ditemukan.'], 404);
        }

        if ($pemusnahan->status !== 'menunggu_konfirmasi') {
            return response()->json(['message' => 'Pemusnahan yang sudah dikonfirmasi tidak bisa diedit.'], 409);
        }

        $identifier = $this->parsePemusnahanIdentifier($request->isbn);

        if (!$identifier['isbn']) {
            return response()->json(['message' => 'Format ISBN/barcode tidak dikenali. Gunakan ISBN atau barcode ISBN/ID copy.'], 422);
        }

        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $identifier['isbn'])
            ->where('is_delete', 0)
            ->select(
                'ISBN as isbn',
                'JUDUL_KOLEKSI as judul_koleksi',
                'KETERANGAN_BUKU as keterangan_buku'
            )
            ->first();

        if (!$buku) {
            return response()->json(['message' => 'ISBN tidak ditemukan atau sudah dihapus.'], 404);
        }

        $copyLegacy = $this->legacyEligibleCopy($identifier);

        if (!$copyLegacy) {
            return response()->json(['message' => 'Copy fisik yang bisa dimusnahkan tidak ditemukan untuk ISBN ini.'], 404);
        }

        $kategoriPemusnahan = $this->getKategoriPemusnahan(
            (object) ['keterangan_buku' => trim(($buku->keterangan_buku ?? '') . ' ' . ($copyLegacy->status_buku ?? ''))],
            $request->alasan
        );

        if (!$kategoriPemusnahan) {
            return response()->json([
                'message' => 'Buku hanya dapat dimusnahkan bila statusnya rusak atau non-aktif. Perbarui keterangan buku terlebih dahulu.'
            ], 422);
        }

        if ($this->legacyPemusnahanExistsForCopy((int) $copyLegacy->id_cp_koleksi, (int) $id)) {
            return response()->json(['message' => 'Buku ini sudah memiliki proses pemusnahan aktif.'], 409);
        }

        DB::table('tr_pemusnahan_buku')
            ->where('ID_PEMUSNAHAN_BUKU', $id)
            ->update([
                'ID_CP_KOLEKSI' => $copyLegacy->id_cp_koleksi,
                'KET_PEMUSNAHAN_BUKU' => $this->legacyPemusnahanDescription($request->nip_karyawan, $kategoriPemusnahan, $request->alasan),
                'TGL_PEMUSNAHAN_BUKU' => Carbon::now(),
                'IS_DELETE' => 0,
            ]);

        Log::info('Data pemusnahan buku diedit.', [
            'id_pemusnahan' => $id,
            'isbn' => $identifier['isbn'],
            'id_cp_koleksi' => $copyLegacy->id_cp_koleksi,
            'judul' => $buku->judul_koleksi,
            'kategori_pemusnahan' => $kategoriPemusnahan,
            'petugas_editor' => $petugas->nip_karyawan,
        ]);

        return response()->json(['message' => 'Data pemusnahan berhasil diperbarui.']);
    }

    private function confirmLegacyPemusnahan(Request $request, $id)
    {
        try {
            $petugas = $this->getPetugasPemusnahan($request->nip_karyawan);

            if (!$this->isPustakawan($petugas)) {
                return response()->json(['message' => 'Konfirmasi pemusnahan hanya dapat dilakukan pustakawan.'], 403);
            }

            $pemusnahan = $this->legacyPemusnahanRow($id);

            if (!$pemusnahan) {
                return response()->json(['message' => 'Data pemusnahan tidak ditemukan.'], 404);
            }

            if ($pemusnahan->status === 'disetujui') {
                return response()->json(['message' => 'Pemusnahan ini sudah dikonfirmasi sebelumnya.'], 409);
            }

            $buku = DB::table('mst_koleksi_buku')
                ->where('ISBN', $pemusnahan->isbn)
                ->where('is_delete', 0)
                ->select(
                    'ISBN as isbn',
                    'JUDUL_KOLEKSI as judul_koleksi',
                    'JUMLAH_EKSEMPLAR as jumlah_eksemplar'
                )
                ->first();

            if (!$buku || (int) $buku->jumlah_eksemplar < 1) {
                return response()->json(['message' => 'Stok buku tidak cukup untuk diproses sebagai pemusnahan.'], 422);
            }

            DB::beginTransaction();

            DB::table('mst_koleksi_buku')
                ->where('ISBN', $pemusnahan->isbn)
                ->decrement('jumlah_eksemplar', 1);

            DB::table('cp_koleksi')
                ->where('ID_CP_KOLEKSI', $pemusnahan->id_cp_koleksi)
                ->update(['STATUS_BUKU' => 'Dimusnahkan']);

            DB::table('tr_pemusnahan_buku')
                ->where('ID_PEMUSNAHAN_BUKU', $id)
                ->update([
                    'TGL_PEMUSNAHAN_BUKU' => Carbon::now(),
                    'IS_DELETE' => 0,
                ]);

            Log::info('Pemusnahan buku dikonfirmasi.', [
                'id_pemusnahan' => $id,
                'isbn' => $pemusnahan->isbn,
                'judul' => $buku->judul_koleksi,
                'petugas_konfirmasi' => $petugas->nip_karyawan,
                'copy_dimusnahkan' => $pemusnahan->id_cp_koleksi,
            ]);

            DB::commit();

            return response()->json(['message' => 'Pemusnahan buku berhasil dikonfirmasi dan tercatat dalam log sistem.']);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json(['message' => 'Gagal mengonfirmasi pemusnahan: ' . $e->getMessage()], 500);
        }
    }

    public function getStats()
    {
        $kategoriLaporan = DB::table('ref_koleksi')
            ->where('NO_KATEGORI_BUKU', '4')
            ->where('IS_DELETE', 0)
            ->value('ID_REF_KOLEKSI');

        $koleksiAktif = DB::table('mst_koleksi_buku')
            ->where('IS_DELETE', 0);

        $totalBuku = (clone $koleksiAktif)
            ->when($kategoriLaporan, function ($query) use ($kategoriLaporan) {
                $query->where(function ($subQuery) use ($kategoriLaporan) {
                    $subQuery->where('ID_REF_KOLEKSI', '!=', $kategoriLaporan)
                        ->orWhereNull('ID_REF_KOLEKSI');
                });
            })
            ->sum(DB::raw('COALESCE(JUMLAH_EKSEMPLAR, 0)'));

        $jumlahSiswa = DB::table('mst_siswa')->where('IS_DELETE', 0)->count();
        $jumlahKaryawan = DB::table('mst_karyawan')->where('IS_DELETE', 0)->count();
        $totalAnggota = $jumlahSiswa + $jumlahKaryawan;

        $totalLaporan = $kategoriLaporan
            ? (clone $koleksiAktif)->where('ID_REF_KOLEKSI', $kategoriLaporan)->count()
            : 0;

        return response()->json([
            'total_buku' => (int) $totalBuku,
            'total_siswa' => $totalAnggota,
            'total_anggota' => $totalAnggota,
            'total_laporan' => (int) $totalLaporan
        ]);
    }

    public function getAnggota(Request $request)
    {
        try {
            // Default 10 data per halaman
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $siswa = DB::table('mst_siswa')
                ->where('is_delete', 0)
                ->select(
                    'nisn_siswa as identitas', 
                    'nama_siswa_tetap as nama', 
                    'gender_siswa as gender', 
                    DB::raw("'Siswa' as role")
                );

            $results = DB::table('mst_karyawan')
                ->where('is_delete', 0)
                ->select(
                    'nip_karyawan as identitas', 
                    'nama_karyawan as nama', 
                    'gender_karyawan as gender', 
                    DB::raw("'Karyawan' as role")
                )
                ->union($siswa)
                ->get(); 

            $sorted = $results->sortBy('nama')->values();
            $currentPageItems = $sorted->forPage($page, $perPage)->values();

            $paginatedData = new LengthAwarePaginator(
                $currentPageItems,
                $sorted->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json($paginatedData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBuku(Request $request)
    {
        try {
            $search = trim((string) $request->query('search', ''));
            $judul = $request->query('judul');
            $penulis = $request->query('penulis');
            $kategori = $request->query('kategori');
            $sortBy = $request->query('sort_by', 'judul_koleksi');
            $sortOrder = $request->query('sort_order', 'asc');
            $perPage = $request->query('per_page', 8);

            $query = DB::table('mst_koleksi_buku')
                ->leftJoin('ref_koleksi', 'mst_koleksi_buku.ID_REF_KOLEKSI', '=', 'ref_koleksi.ID_REF_KOLEKSI')
                ->select([
                    'mst_koleksi_buku.ISBN',
                    'mst_koleksi_buku.JUDUL_KOLEKSI as judul_koleksi',
                    'mst_koleksi_buku.PENGARANG as pengarang',
                    'mst_koleksi_buku.PENERBIT as penerbit',
                    'mst_koleksi_buku.TAHUN as tahun',
                    'mst_koleksi_buku.NB_KOLEKSI as nb_koleksi',
                    'mst_koleksi_buku.TGL_MASUK_KOLEKSI as tgl_masuk_koleksi',
                    'mst_koleksi_buku.JUMLAH_EKSEMPLAR as jumlah_eksemplar',
                    'mst_koleksi_buku.JUMLAH_HALAMAN as jumlah_halaman',
                    'mst_koleksi_buku.UKURAN_BUKU as ukuran_buku',
                    'mst_koleksi_buku.BIBLIOGRAFI as bibliografi',
                    'mst_koleksi_buku.INDEKS_AWAL_AKHIR as indeks_awal_akhir',
                    'mst_koleksi_buku.KETERANGAN_BUKU as keterangan_buku',
                    'mst_koleksi_buku.NO_RAK_BUKU as no_rak_buku',
                    'mst_koleksi_buku.IS_DELETE as is_delete',
                    'mst_koleksi_buku.ID_REF_KOLEKSI as id_ref_koleksi',
                    'ref_koleksi.DESKRIPSI_KATEGORI as kategori',
                ])
                ->where('mst_koleksi_buku.IS_DELETE', 0)
                ->where(function ($query) {
                    $query->where('ref_koleksi.NO_KATEGORI_BUKU', '!=', '4')
                        ->orWhereNull('ref_koleksi.NO_KATEGORI_BUKU');
                });

            if ($search !== '') {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('mst_koleksi_buku.JUDUL_KOLEKSI', 'LIKE', "%{$search}%")
                        ->orWhere('mst_koleksi_buku.PENGARANG', 'LIKE', "%{$search}%")
                        ->orWhere('mst_koleksi_buku.ISBN', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($judul)) {
                $query->where('mst_koleksi_buku.JUDUL_KOLEKSI', 'LIKE', "%{$judul}%");
            }

            if (!empty($penulis)) {
                $query->where('mst_koleksi_buku.PENGARANG', 'LIKE', "%{$penulis}%");
            }

            if (!empty($kategori)) {
                $query->where('mst_koleksi_buku.ID_REF_KOLEKSI', $kategori);
            }

            $allowedSort = [
                'judul_koleksi' => 'mst_koleksi_buku.JUDUL_KOLEKSI',
                'pengarang' => 'mst_koleksi_buku.PENGARANG',
                'tahun' => 'mst_koleksi_buku.TAHUN',
                'id_ref_koleksi' => 'mst_koleksi_buku.ID_REF_KOLEKSI',
                'kategori' => 'ref_koleksi.DESKRIPSI_KATEGORI',
            ];
            $sortColumn = $allowedSort[$sortBy] ?? $allowedSort['judul_koleksi'];
            $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';

            return response()->json($query->orderBy($sortColumn, $sortOrder)->paginate($perPage));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getKategoriBuku()
    {
        try {
            $kategori = DB::table('ref_koleksi')
                ->where('IS_DELETE', 0)
                ->where(function ($query) {
                    $query->where('NO_KATEGORI_BUKU', '!=', '4')
                        ->orWhereNull('NO_KATEGORI_BUKU');
                })
                ->orderBy('DESKRIPSI_KATEGORI')
                ->get([
                    'ID_REF_KOLEKSI as id_ref_koleksi',
                    'DESKRIPSI_KATEGORI as deskripsi',
                ]);

            return response()->json($kategori);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateBuku(Request $request, string $isbn)
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'judul_koleksi' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:25'],
            'penerbit' => ['required', 'string', 'max:25'],
            'tahun' => ['required', 'digits:4'],
            'jumlah_eksemplar' => ['required', 'integer', 'min:0'],
            'no_rak_buku' => ['required', 'string', 'max:100'],
            'keterangan_buku' => ['nullable', 'string', 'max:255'],
            'id_ref_koleksi' => ['required', 'integer', Rule::exists('ref_koleksi', 'id_ref_koleksi')->where('is_delete', 0)],
        ], [
            'editor_nip_karyawan.required' => 'Identitas pustakawan wajib dikirim.',
            'id_ref_koleksi.exists' => 'Kategori buku tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pustakawan = DB::table('mst_karyawan')
            ->where('nip_karyawan', $request->editor_nip_karyawan)
            ->where('is_delete', 0)
            ->first();

        if (!$pustakawan || strtolower((string) $pustakawan->jabatan_fungsional) !== 'pustakawan') {
            return response()->json([
                'message' => 'Hanya pustakawan yang dapat mengubah koleksi buku.',
            ], 403);
        }

        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('is_delete', 0)
            ->first();

        if (!$buku) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->update([
                'judul_koleksi' => $request->judul_koleksi,
                'pengarang' => $request->pengarang,
                'penerbit' => $request->penerbit,
                'tahun' => $request->tahun,
                'jumlah_eksemplar' => $request->jumlah_eksemplar,
                'no_rak_buku' => $request->no_rak_buku,
                'keterangan_buku' => $request->keterangan_buku ?: '',
                'id_ref_koleksi' => $request->id_ref_koleksi,
            ]);

        $updatedBook = DB::table('mst_koleksi_buku')
            ->join('ref_koleksi', 'mst_koleksi_buku.id_ref_koleksi', '=', 'ref_koleksi.id_ref_koleksi')
            ->where('mst_koleksi_buku.ISBN', $isbn)
            ->select(
                'mst_koleksi_buku.ISBN',
                'mst_koleksi_buku.judul_koleksi',
                'mst_koleksi_buku.pengarang',
                'mst_koleksi_buku.penerbit',
                'mst_koleksi_buku.tahun',
                'mst_koleksi_buku.jumlah_eksemplar',
                'mst_koleksi_buku.no_rak_buku',
                'mst_koleksi_buku.keterangan_buku',
                'mst_koleksi_buku.id_ref_koleksi',
                'ref_koleksi.deskripsi as kategori'
            )
            ->first();

        return response()->json([
            'message' => 'Koleksi buku berhasil diperbarui.',
            'data' => $updatedBook,
        ]);
    }

    public function destroyBuku(Request $request, string $isbn)
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
        ], [
            'editor_nip_karyawan.required' => 'Identitas pustakawan wajib dikirim.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pustakawan = DB::table('mst_karyawan')
            ->where('nip_karyawan', $request->editor_nip_karyawan)
            ->where('is_delete', 0)
            ->first();

        if (!$pustakawan || strtolower((string) $pustakawan->jabatan_fungsional) !== 'pustakawan') {
            return response()->json([
                'message' => 'Hanya pustakawan yang dapat menghapus koleksi buku.',
            ], 403);
        }

        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $isbn)
            ->where('is_delete', 0)
            ->first();

        if (!$buku) {
            return response()->json([
                'message' => 'Data buku tidak ditemukan.',
            ], 404);
        }

        $sedangDipinjam = DB::table('cp_koleksi')
            ->join('tr_peminjaman', 'cp_koleksi.id_cp_koleksi', '=', 'tr_peminjaman.id_cp_koleksi')
            ->where('cp_koleksi.ISBN', $isbn)
            ->whereNull('tr_peminjaman.tgl_kembali')
            ->exists();

        if ($sedangDipinjam) {
            return response()->json([
                'message' => 'Buku tidak bisa dihapus karena sedang dipinjam.',
            ], 409);
        }

        $punyaRiwayatPeminjaman = DB::table('cp_koleksi')
            ->where('ISBN', $isbn)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tr_peminjaman')
                    ->whereColumn('tr_peminjaman.id_cp_koleksi', 'cp_koleksi.id_cp_koleksi');
            })
            ->exists();

        try {
            DB::transaction(function () use ($isbn, $punyaRiwayatPeminjaman) {
                DB::table('mst_koleksi_buku')
                    ->where('ISBN', $isbn)
                    ->update([
                        'is_delete' => 1,
                    ]);

                if ($punyaRiwayatPeminjaman) {
                    DB::table('cp_koleksi')
                        ->where('ISBN', $isbn)
                        ->update(['status_buku' => 'Nonaktif']);

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

    /**
     * ==========================================
     * TAMBAHAN FITUR: PEMUSNAHAN BUKU (ISBN)
     * ==========================================
     */

    public function storePemusnahan(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
            'alasan' => 'required|string',
            'nip_karyawan' => 'required|string'
        ]);

        try {
            $petugas = $this->getPetugasPemusnahan($request->nip_karyawan);

            if (!$this->isPustakawan($petugas)) {
                return response()->json(['message' => 'Hanya pustakawan yang dapat mencatat pemusnahan buku.'], 403);
            }

            $identifier = $this->parsePemusnahanIdentifier($request->isbn);

            if (!$identifier['isbn']) {
                return response()->json(['message' => 'Format ISBN/barcode tidak dikenali. Gunakan ISBN atau barcode ISBN/ID copy.'], 422);
            }

            $buku = DB::table('mst_koleksi_buku')
                ->where('ISBN', $identifier['isbn'])
                ->where('is_delete', 0)
                ->select(
                    'ISBN as isbn',
                    'JUDUL_KOLEKSI as judul_koleksi',
                    'KETERANGAN_BUKU as keterangan_buku',
                    'JUMLAH_EKSEMPLAR as jumlah_eksemplar'
                )
                ->first();

            if (!$buku) {
                return response()->json(['message' => 'ISBN tidak ditemukan atau sudah dihapus.'], 404);
            }

            $copy = null;
            if ($identifier['id_cp_koleksi']) {
                $copy = DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $identifier['id_cp_koleksi'])
                    ->where('ISBN', $identifier['isbn'])
                    ->select(
                        'ID_CP_KOLEKSI as id_cp_koleksi',
                        'ISBN',
                        'STATUS_BUKU as status_buku'
                    )
                    ->first();

                if (!$copy) {
                    return response()->json(['message' => 'Copy fisik dari barcode tidak ditemukan untuk ISBN ini.'], 404);
                }
            }

            $copyAktifDipinjam = DB::table('cp_koleksi')
                ->where('cp_koleksi.ISBN', $identifier['isbn'])
                ->when($identifier['id_cp_koleksi'], function ($query) use ($identifier) {
                    $query->where('cp_koleksi.ID_CP_KOLEKSI', $identifier['id_cp_koleksi']);
                })
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tr_peminjaman')
                        ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                        ->whereNull('tr_peminjaman.TGL_KEMBALI')
                        ->whereIn('tr_peminjaman.STATUS_PEMINJAMAN', ['Dipinjam', 'Terlambat']);
                })
                ->exists();

            if ($copyAktifDipinjam) {
                return response()->json(['message' => 'Buku masih sedang dipinjam. Proses pengembalian terlebih dahulu sebelum pemusnahan.'], 422);
            }

            $kondisiCopy = $copy?->status_buku ?? '';
            $kategoriPemusnahan = $this->getKategoriPemusnahan(
                (object) ['keterangan_buku' => trim(($buku->keterangan_buku ?? '') . ' ' . $kondisiCopy)],
                $request->alasan
            );

            if (!$kategoriPemusnahan) {
                return response()->json([
                    'message' => 'Buku hanya dapat dimusnahkan bila statusnya rusak atau non-aktif. Perbarui keterangan buku terlebih dahulu.'
                ], 422);
            }

            $copyLegacy = $this->legacyEligibleCopy($identifier);

            if (!$copyLegacy) {
                return response()->json(['message' => 'Copy fisik yang bisa dimusnahkan tidak ditemukan untuk ISBN ini.'], 404);
            }

            if ($this->legacyPemusnahanExistsForCopy((int) $copyLegacy->id_cp_koleksi)) {
                return response()->json(['message' => 'Buku ini sudah memiliki proses pemusnahan aktif.'], 409);
            }

            DB::transaction(function () use ($copyLegacy, $kategoriPemusnahan, $request) {
                DB::table('tr_pemusnahan_buku')->insert([
                    'ID_CP_KOLEKSI' => $copyLegacy->id_cp_koleksi,
                    'KET_PEMUSNAHAN_BUKU' => $this->legacyPemusnahanDescription($request->nip_karyawan, $kategoriPemusnahan, $request->alasan),
                    'TGL_PEMUSNAHAN_BUKU' => Carbon::now(),
                    'IS_DELETE' => 0,
                ]);
            });

            Log::info('Pemusnahan buku diajukan.', [
                'isbn' => $identifier['isbn'],
                'id_cp_koleksi' => $copyLegacy->id_cp_koleksi,
                'judul' => $buku->judul_koleksi,
                'kategori_pemusnahan' => $kategoriPemusnahan,
                'petugas_pengaju' => $petugas->nip_karyawan,
            ]);

            return response()->json(['message' => 'Pengajuan pemusnahan berhasil dicatat dan menunggu konfirmasi admin.'], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getHistoryPemusnahan(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status');

        $query = $this->legacyPemusnahanBaseQuery();

        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('tr_pemusnahan.isbn', 'like', "%$search%")
                  ->orWhere('tr_pemusnahan.id', 'like', "%$search%")
                  ->orWhere('tr_pemusnahan.id_cp_koleksi', 'like', "%$search%")
                  ->orWhere(DB::raw("CONCAT(tr_pemusnahan.isbn, '/', COALESCE(tr_pemusnahan.id_cp_koleksi, ''))"), 'like', "%$search%")
                  ->orWhere('mst_koleksi_buku.judul_koleksi', 'like', "%$search%")
                  ->orWhere('mst_koleksi_buku.no_rak_buku', 'like', "%$search%")
                  ->orWhere('tr_pemusnahan.alasan', 'like', "%$search%")
                  ->orWhere('tr_pemusnahan.status', 'like', "%$search%")
                  ->orWhere('tr_pemusnahan.nip_karyawan', 'like', "%$search%")
                  ->orWhere('petugas.nama_karyawan', 'like', "%$search%");
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('copy.ISBN', 'like', "%$search%")
                    ->orWhere('buku.judul_koleksi', 'like', "%$search%")
                    ->orWhere('pemusnahan.KET_PEMUSNAHAN_BUKU', 'like', "%$search%");
            });
        }

        if ($status === 'disetujui') {
            $query->where('copy.STATUS_BUKU', 'Dimusnahkan');
        } elseif ($status === 'menunggu_konfirmasi') {
            $query->where(function ($q) {
                $q->whereNull('copy.STATUS_BUKU')
                    ->orWhere('copy.STATUS_BUKU', '!=', 'Dimusnahkan');
            });
        }

        return response()->json(
            $query->orderBy('pemusnahan.TGL_PEMUSNAHAN_BUKU', 'desc')
                ->get()
                ->map(fn ($row) => $this->normalizeLegacyPemusnahanRow($row))
                ->values()
        );
    }

    public function getBukuRusak()
    {
        $rusakDariKeterangan = DB::table('mst_koleksi_buku')
            ->where('is_delete', 0)
            ->where('keterangan_buku', 'like', '%Rusak%')
            ->select(
                'ISBN as isbn',
                'judul_koleksi as judul',
                'keterangan_buku as kondisi'
            )
            ->get();

        $rusakDariCopy = DB::table('cp_koleksi')
            ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
            ->where('mst_koleksi_buku.is_delete', 0)
            ->whereIn('cp_koleksi.STATUS_BUKU', ['Rusak', 'Hilang', 'Nonaktif'])
            ->select(
                DB::raw("CONCAT(cp_koleksi.ISBN, '/', cp_koleksi.ID_CP_KOLEKSI) as isbn"),
                'mst_koleksi_buku.judul_koleksi as judul',
                'cp_koleksi.STATUS_BUKU as kondisi'
            )
            ->get();

        return response()->json($rusakDariKeterangan->merge($rusakDariCopy)->values());
    }

    public function getBukuOverdue()
    {
        return response()->json(
            DB::table('tr_peminjaman')
                ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
                ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
                ->whereNull('tr_peminjaman.tgl_kembali')
                ->where('tr_peminjaman.tgl_harus_kembali', '<', Carbon::now()->subDays(30))
                ->select(
                    'mst_koleksi_buku.ISBN as isbn', 
                    'mst_koleksi_buku.judul_koleksi as judul',
                    DB::raw('DATEDIFF(NOW(), tr_peminjaman.tgl_harus_kembali) as hari_terlambat')
                )
                ->get()
        );
    }

    public function updatePemusnahan(Request $request, $id)
    {
        $request->validate([
            'isbn' => 'required|string',
            'alasan' => 'required|string',
            'nip_karyawan' => 'required|string',
            'tanggal_pemusnahan' => 'nullable|date',
        ]);

        $petugas = $this->getPetugasPemusnahan($request->nip_karyawan);

        if (!$this->isPustakawan($petugas)) {
            return response()->json(['message' => 'Hanya pustakawan yang dapat mengubah proses pemusnahan buku.'], 403);
        }

        $pemusnahan = DB::table('tr_pemusnahan')->where('id', $id)->first();

        if (!$pemusnahan || $pemusnahan->status === 'soft_deleted') {
            return response()->json(['message' => 'Data pemusnahan tidak ditemukan.'], 404);
        }

        if (!in_array($pemusnahan->status, ['menunggu_konfirmasi', 'disetujui'], true)) {
            return response()->json(['message' => 'Status pemusnahan ini tidak bisa diedit.'], 409);
        }

        $identifier = $this->parsePemusnahanIdentifier($request->isbn);

        if (!$identifier['isbn']) {
            return response()->json(['message' => 'Format ISBN/barcode tidak dikenali. Gunakan ISBN atau barcode ISBN/ID copy.'], 422);
        }

        if (
            $pemusnahan->status === 'disetujui' &&
            (
                (string) $identifier['isbn'] !== (string) $pemusnahan->isbn ||
                (string) ($identifier['id_cp_koleksi'] ?? '') !== (string) ($pemusnahan->id_cp_koleksi ?? '')
            )
        ) {
            return response()->json([
                'message' => 'ISBN/copy pemusnahan yang sudah disetujui tidak bisa diubah agar stok dan status copy tetap sesuai.'
            ], 409);
        }

        $buku = DB::table('mst_koleksi_buku')
            ->where('ISBN', $identifier['isbn'])
            ->where('is_delete', 0)
            ->select(
                'ISBN as isbn',
                'JUDUL_KOLEKSI as judul_koleksi',
                'KETERANGAN_BUKU as keterangan_buku'
            )
            ->first();

        if (!$buku) {
            return response()->json(['message' => 'ISBN tidak ditemukan atau sudah dihapus.'], 404);
        }

        $copy = null;
        if ($identifier['id_cp_koleksi']) {
            $copy = DB::table('cp_koleksi')
                ->where('ID_CP_KOLEKSI', $identifier['id_cp_koleksi'])
                ->where('ISBN', $identifier['isbn'])
                ->select(
                    'ID_CP_KOLEKSI as id_cp_koleksi',
                    'STATUS_BUKU as status_buku'
                )
                ->first();

            if (!$copy) {
                return response()->json(['message' => 'Copy fisik dari barcode tidak ditemukan untuk ISBN ini.'], 404);
            }
        }

        $copyAktifDipinjam = DB::table('cp_koleksi')
            ->where('cp_koleksi.ISBN', $identifier['isbn'])
            ->when($identifier['id_cp_koleksi'], function ($query) use ($identifier) {
                $query->where('cp_koleksi.ID_CP_KOLEKSI', $identifier['id_cp_koleksi']);
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tr_peminjaman')
                    ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                    ->whereNull('tr_peminjaman.TGL_KEMBALI')
                    ->whereIn('tr_peminjaman.STATUS_PEMINJAMAN', ['Dipinjam', 'Terlambat']);
            })
            ->exists();

        if ($copyAktifDipinjam) {
            return response()->json(['message' => 'Buku masih sedang dipinjam. Proses pengembalian terlebih dahulu sebelum pemusnahan.'], 422);
        }

        $kondisiCopy = $copy?->status_buku ?? '';
        $kategoriPemusnahan = $this->getKategoriPemusnahan(
            (object) ['keterangan_buku' => trim(($buku->keterangan_buku ?? '') . ' ' . $kondisiCopy)],
            $request->alasan
        );

        if (!$kategoriPemusnahan) {
            return response()->json([
                'message' => 'Buku hanya dapat dimusnahkan bila statusnya rusak atau non-aktif. Perbarui keterangan buku terlebih dahulu.'
            ], 422);
        }

        $existing = DB::table('tr_pemusnahan')
            ->where('id', '!=', $id)
            ->where('isbn', $identifier['isbn'])
            ->when($identifier['id_cp_koleksi'], function ($query) use ($identifier) {
                $query->where('id_cp_koleksi', $identifier['id_cp_koleksi']);
            })
            ->whereIn('status', ['menunggu_konfirmasi', 'disetujui'])
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'Buku ini sudah memiliki proses pemusnahan aktif.'], 409);
        }

        DB::table('tr_pemusnahan')->where('id', $id)->update([
            'isbn' => $identifier['isbn'],
            'id_cp_koleksi' => $identifier['id_cp_koleksi'],
            'alasan' => '[' . $kategoriPemusnahan . '] ' . trim($request->alasan),
            'nip_karyawan' => $request->nip_karyawan,
            'tanggal_pemusnahan' => $request->filled('tanggal_pemusnahan')
                ? Carbon::parse($request->tanggal_pemusnahan)
                : $pemusnahan->tanggal_pemusnahan,
            'updated_at' => Carbon::now(),
        ]);

        Log::info('Data pemusnahan buku diedit.', [
            'id_pemusnahan' => $id,
            'isbn' => $identifier['isbn'],
            'id_cp_koleksi' => $identifier['id_cp_koleksi'],
            'judul' => $buku->judul_koleksi,
            'kategori_pemusnahan' => $kategoriPemusnahan,
            'petugas_editor' => $petugas->nip_karyawan,
        ]);

        $updated = DB::table('tr_pemusnahan')
            ->join('mst_koleksi_buku', 'tr_pemusnahan.isbn', '=', 'mst_koleksi_buku.ISBN')
            ->leftJoin('mst_karyawan as petugas', 'tr_pemusnahan.nip_karyawan', '=', 'petugas.nip_karyawan')
            ->where('tr_pemusnahan.id', $id)
            ->select(
                'tr_pemusnahan.*',
                'mst_koleksi_buku.judul_koleksi as judul',
                'mst_koleksi_buku.keterangan_buku',
                'mst_koleksi_buku.no_rak_buku',
                'petugas.nama_karyawan as nama_petugas'
            )
            ->first();

        return response()->json([
            'message' => 'Data pemusnahan berhasil diperbarui.',
            'data' => $updated,
        ]);
        return $this->updateLegacyPemusnahan($request, $id, $petugas);
    }

    public function updateStatusPemusnahan(Request $request, $id)
    {
        $statusBaru = $request->get('status', 'soft_deleted');

        if ($statusBaru !== 'soft_deleted') {
            return response()->json(['message' => 'Status hanya bisa diarsipkan atau dikonfirmasi lewat tombol Konfirmasi.'], 422);
        }

        DB::table('tr_pemusnahan_buku')->where('ID_PEMUSNAHAN_BUKU', $id)->update([
            'IS_DELETE' => 1,
        ]);

        Log::info('Status pemusnahan diperbarui.', [
            'id_pemusnahan' => $id,
            'status_baru' => $statusBaru,
        ]);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function getAnggotaByIdentifier($identifier)
    {
        // 1. Cari di tabel siswa berdasarkan NISN
        $siswa = DB::table('mst_siswa')
                    ->where('nisn_siswa', $identifier)
                    ->where('is_delete', 0)
                    ->first();

        if ($siswa) {
            return response()->json($siswa);
        }

        // 2. Jika tidak ada, cari di tabel karyawan berdasarkan NIP
        $karyawan = DB::table('mst_karyawan')
                        ->where('nip_karyawan', $identifier)
                        ->where('is_delete', 0)
                        ->first();

        if ($karyawan) {
            return response()->json($karyawan);
        }

        // 3. Jika keduanya tidak ada
        return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }
        public function confirmPemusnahan(Request $request, $id)
        {
            $request->validate([
                'nip_karyawan' => 'required|string',
            ]);

            return $this->confirmLegacyPemusnahan($request, $id);
        }
    
        public function printBeritaAcaraPemusnahan($id)
        {
            $data = $this->legacyPemusnahanRow($id);

            abort_unless($data && $data->status === 'disetujui', 404);
    
            return view('pustakawan.pemusnahan.berita_acara', [
                'data' => $data,
                'tanggalCetak' => Carbon::now('Asia/Jakarta'),
            ]);
        }

        public function exportPdfBeritaAcaraPemusnahan($id)
        {
            $data = $this->legacyPemusnahanRow($id);

            abort_unless($data && $data->status === 'disetujui', 404);

            $tanggalCetak = Carbon::now('Asia/Jakarta');
            $pdf = Pdf::loadView('pustakawan.pemusnahan.berita_acara_pdf', [
                'data' => $data,
                'tanggalCetak' => $tanggalCetak,
            ]);

            return $pdf->setPaper('a4', 'portrait')
                ->download('Berita_Acara_Pemusnahan_' . str_pad((string) $data->id, 4, '0', STR_PAD_LEFT) . '.pdf');
        }
}

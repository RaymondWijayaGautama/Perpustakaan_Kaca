<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon; // Tambahan untuk pengolahan tanggal

class DashboardController extends Controller
{
    private function getPetugasPemusnahan(?string $nipKaryawan)
    {
        if (!$nipKaryawan) {
            return null;
        }

        return DB::table('mst_karyawan')
            ->where('NIP_KARYAWAN', $nipKaryawan)
            ->where('IS_DELETE', 0)
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

    private function normalizeIsbnInput(?string $isbn): string
    {
        return strtoupper(preg_replace('/[^0-9X]/i', '', (string) $isbn));
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

    public function getStats()
    {
        // 1. Menghitung jumlah total fisik buku (Kecuali kategori Laporan/PKL ID 4)
        $totalBuku = DB::table('mst_koleksi_buku')
            ->where('is_delete', 0)
            ->where('id_ref_koleksi', '!=', 4) 
            ->sum('jumlah_eksemplar');

        // 2. Menghitung total anggota (Siswa + Karyawan)
        $jumlahSiswa = DB::table('mst_siswa')->where('is_delete', 0)->count();
        $jumlahKaryawan = DB::table('mst_karyawan')->where('is_delete', 0)->count();

        // 3. Menghitung total Laporan PKL (id_ref_koleksi = 4)
        $totalLaporan = DB::table('mst_koleksi_buku')
            ->where('is_delete', 0)
            ->where('id_ref_koleksi', 4)
            ->count();

        return response()->json([
            'total_buku' => $totalBuku,
            'total_siswa' => $jumlahSiswa + $jumlahKaryawan,
            'total_laporan' => $totalLaporan
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
                    'mst_koleksi_buku.JUMLAH_EKSEMPLAR as jumlah_ekslempar',
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
                ->where('mst_koleksi_buku.ID_REF_KOLEKSI', '!=', 4);

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
                ->where('ID_REF_KOLEKSI', '!=', 4)
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
            'jumlah_ekslempar' => ['required', 'integer', 'min:0'],
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
                'jumlah_ekslempar' => $request->jumlah_ekslempar,
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
                'mst_koleksi_buku.jumlah_ekslempar',
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
            DB::beginTransaction();

            $isbn = $this->normalizeIsbnInput($request->isbn);
            $petugas = $this->getPetugasPemusnahan($request->nip_karyawan);

            if (!$this->isPustakawan($petugas)) {
                DB::rollBack();
                return response()->json(['message' => 'Hanya pustakawan yang dapat mencatat pemusnahan buku.'], 403);
            }

            $buku = DB::table('mst_koleksi_buku')
                ->where('ISBN', $isbn)
                ->where('IS_DELETE', 0)
                ->select(
                    'ISBN',
                    'JUDUL_KOLEKSI as judul_koleksi',
                    'KETERANGAN_BUKU as keterangan_buku'
                )
                ->first();

            if (!$buku) {
                DB::rollBack();
                return response()->json(['message' => 'ISBN tidak ditemukan atau sudah dihapus.'], 404);
            }

            $kategoriPemusnahan = $this->getKategoriPemusnahan($buku, $request->alasan);

            if (!$kategoriPemusnahan) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Buku hanya dapat dimusnahkan bila statusnya rusak atau non-aktif. Perbarui keterangan buku terlebih dahulu.'
                ], 422);
            }

            $copy = DB::table('cp_koleksi')
                ->where('ISBN', $isbn)
                ->where(function ($copyQuery) {
                    $copyQuery->whereNull('STATUS_BUKU')
                        ->orWhere('STATUS_BUKU', '!=', 'Dimusnahkan');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tr_peminjaman')
                        ->whereColumn('tr_peminjaman.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                        ->whereNull('tr_peminjaman.TGL_KEMBALI')
                        ->whereIn('tr_peminjaman.STATUS_PEMINJAMAN', ['Dipinjam', 'Terlambat']);
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tr_pemusnahan_buku')
                        ->whereColumn('tr_pemusnahan_buku.ID_CP_KOLEKSI', 'cp_koleksi.ID_CP_KOLEKSI')
                        ->where(function ($activeQuery) {
                            $activeQuery->whereNull('tr_pemusnahan_buku.IS_DELETE')
                                ->orWhere('tr_pemusnahan_buku.IS_DELETE', 0);
                        });
                })
                ->orderByDesc('ID_CP_KOLEKSI')
                ->first();

            if (!$copy) {
                DB::rollBack();
                return response()->json(['message' => 'Tidak ada copy buku yang tersedia untuk proses pemusnahan.'], 409);
            }

            DB::table('tr_pemusnahan_buku')->insert([
                'ID_CP_KOLEKSI' => $copy->ID_CP_KOLEKSI,
                'KET_PEMUSNAHAN_BUKU' => '[' . $kategoriPemusnahan . '] ' . trim($request->alasan),
                'TGL_PEMUSNAHAN_BUKU' => Carbon::now(),
                'IS_DELETE' => 0,
            ]);

            Log::info('Pemusnahan buku diajukan.', [
                'isbn' => $isbn,
                'judul' => $buku->judul_koleksi,
                'kategori_pemusnahan' => $kategoriPemusnahan,
                'petugas_pengaju' => $petugas->nip_karyawan,
                'copy_diajukan' => $copy->ID_CP_KOLEKSI,
            ]);

            DB::commit();
            return response()->json(['message' => 'Pengajuan pemusnahan berhasil dicatat dan menunggu konfirmasi admin.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getHistoryPemusnahan(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = DB::table('tr_pemusnahan_buku as pemusnahan')
            ->join('cp_koleksi', 'pemusnahan.ID_CP_KOLEKSI', '=', 'cp_koleksi.ID_CP_KOLEKSI')
            ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
            ->select(
                'pemusnahan.ID_PEMUSNAHAN_BUKU as id',
                'cp_koleksi.ISBN as isbn',
                'pemusnahan.KET_PEMUSNAHAN_BUKU as alasan',
                'pemusnahan.TGL_PEMUSNAHAN_BUKU as tanggal_pemusnahan',
                'pemusnahan.TGL_PEMUSNAHAN_BUKU as updated_at',
                'mst_koleksi_buku.JUDUL_KOLEKSI as judul',
                'mst_koleksi_buku.KETERANGAN_BUKU as keterangan_buku',
                'mst_koleksi_buku.NO_RAK_BUKU as no_rak_buku',
                DB::raw("'Pustakawan' as nama_petugas"),
                DB::raw("NULL as nip_karyawan"),
                DB::raw("CASE WHEN cp_koleksi.STATUS_BUKU = 'Dimusnahkan' THEN 'disetujui' ELSE 'menunggu_konfirmasi' END as status")
            )
            ->where(function ($activeQuery) {
                $activeQuery->whereNull('pemusnahan.IS_DELETE')
                    ->orWhere('pemusnahan.IS_DELETE', 0);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('cp_koleksi.ISBN', 'like', "%$search%")
                  ->orWhere('mst_koleksi_buku.JUDUL_KOLEKSI', 'like', "%$search%")
                  ->orWhere('pemusnahan.KET_PEMUSNAHAN_BUKU', 'like', "%$search%");
            });
        }

        if ($status && $status !== 'semua') {
            if ($status === 'menunggu_konfirmasi') {
                $query->where(function ($statusQuery) {
                    $statusQuery->whereNull('cp_koleksi.STATUS_BUKU')
                        ->orWhere('cp_koleksi.STATUS_BUKU', '!=', 'Dimusnahkan');
                });
            } elseif ($status === 'disetujui') {
                $query->where('cp_koleksi.STATUS_BUKU', 'Dimusnahkan');
            }
        }

        return response()->json($query->orderByDesc('pemusnahan.ID_PEMUSNAHAN_BUKU')->get());
    }

    public function getBukuRusak()
    {
        return response()->json(
            DB::table('mst_koleksi_buku')
                ->where('IS_DELETE', 0)
                ->where('KETERANGAN_BUKU', 'like', '%Rusak%')
                ->select('ISBN as isbn', 'JUDUL_KOLEKSI as judul', 'KETERANGAN_BUKU as kondisi')
                ->get()
        );
    }

    public function getBukuOverdue()
    {
        return response()->json(
            DB::table('tr_peminjaman')
                ->join('cp_koleksi', 'tr_peminjaman.ID_CP_KOLEKSI', '=', 'cp_koleksi.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
                ->whereNull('tr_peminjaman.TGL_KEMBALI')
                ->where('tr_peminjaman.TGL_HARUS_KEMBALI', '<', Carbon::now()->subDays(30))
                ->select(
                    'mst_koleksi_buku.ISBN as isbn', 
                    'mst_koleksi_buku.JUDUL_KOLEKSI as judul',
                    DB::raw('DATEDIFF(NOW(), tr_peminjaman.TGL_HARUS_KEMBALI) as hari_terlambat')
                )
                ->distinct()
                ->get()
        );
    }

    public function updateStatusPemusnahan(Request $request, $id)
    {
        $statusBaru = $request->get('status', 'soft_deleted');

        if ($statusBaru === 'soft_deleted') {
            DB::table('tr_pemusnahan_buku')->where('ID_PEMUSNAHAN_BUKU', $id)->update([
                'IS_DELETE' => 1,
            ]);
        }

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
    
            try {
                DB::beginTransaction();
    
                $petugas = $this->getPetugasPemusnahan($request->nip_karyawan);
    
                if (!$this->isPustakawan($petugas)) {
                    DB::rollBack();
                    return response()->json(['message' => 'Konfirmasi pemusnahan hanya dapat dilakukan pustakawan.'], 403);
                }
    
                $pemusnahan = DB::table('tr_pemusnahan_buku as pemusnahan')
                    ->join('cp_koleksi', 'pemusnahan.ID_CP_KOLEKSI', '=', 'cp_koleksi.ID_CP_KOLEKSI')
                    ->where('pemusnahan.ID_PEMUSNAHAN_BUKU', $id)
                    ->select(
                        'pemusnahan.*',
                        'cp_koleksi.ISBN as isbn',
                        'cp_koleksi.STATUS_BUKU as status_buku'
                    )
                    ->first();
    
                if (!$pemusnahan || (int) $pemusnahan->IS_DELETE === 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Data pemusnahan tidak ditemukan.'], 404);
                }
    
                if ($pemusnahan->status_buku === 'Dimusnahkan') {
                    DB::rollBack();
                    return response()->json(['message' => 'Pemusnahan ini sudah dikonfirmasi sebelumnya.'], 409);
                }
    
                $buku = DB::table('mst_koleksi_buku')
                    ->where('ISBN', $pemusnahan->isbn)
                    ->where('IS_DELETE', 0)
                    ->select(
                        'ISBN',
                        'JUDUL_KOLEKSI as judul_koleksi',
                        'JUMLAH_EKSEMPLAR as jumlah_eksemplar'
                    )
                    ->first();
    
                if (!$buku || (int) $buku->jumlah_eksemplar < 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Stok buku tidak cukup untuk diproses sebagai pemusnahan.'], 422);
                }
    
                DB::table('mst_koleksi_buku')
                    ->where('ISBN', $pemusnahan->isbn)
                    ->decrement('JUMLAH_EKSEMPLAR', 1);

                DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $pemusnahan->ID_CP_KOLEKSI)
                    ->update(['STATUS_BUKU' => 'Dimusnahkan']);
    
                DB::table('tr_pemusnahan_buku')
                    ->where('ID_PEMUSNAHAN_BUKU', $id)
                    ->update([
                        'TGL_PEMUSNAHAN_BUKU' => Carbon::now(),
                    ]);
    
                Log::info('Pemusnahan buku dikonfirmasi.', [
                    'id_pemusnahan' => $id,
                    'isbn' => $pemusnahan->isbn,
                    'judul' => $buku->judul_koleksi,
                    'petugas_konfirmasi' => $petugas->nip_karyawan,
                    'copy_dimusnahkan' => $pemusnahan->ID_CP_KOLEKSI,
                ]);
    
                DB::commit();
    
                return response()->json(['message' => 'Pemusnahan buku berhasil dikonfirmasi dan tercatat dalam log sistem.']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Gagal mengonfirmasi pemusnahan: ' . $e->getMessage()], 500);
            }
        }
    
        public function printBeritaAcaraPemusnahan($id)
        {
            $data = DB::table('tr_pemusnahan_buku as pemusnahan')
                ->join('cp_koleksi as copy', 'pemusnahan.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->select(
                    'pemusnahan.ID_PEMUSNAHAN_BUKU as id',
                    'copy.ISBN as isbn',
                    'pemusnahan.KET_PEMUSNAHAN_BUKU as alasan',
                    'pemusnahan.TGL_PEMUSNAHAN_BUKU as tanggal_pemusnahan',
                    DB::raw("'disetujui' as status"),
                    DB::raw("'Pustakawan' as nama_petugas"),
                    DB::raw("NULL as nip_karyawan"),
                    'buku.JUDUL_KOLEKSI as judul_koleksi',
                    'buku.PENGARANG as pengarang',
                    'buku.PENERBIT as penerbit',
                    'buku.NO_RAK_BUKU as no_rak_buku',
                    'buku.KETERANGAN_BUKU as keterangan_buku'
                )
                ->where('pemusnahan.ID_PEMUSNAHAN_BUKU', $id)
                ->where('copy.STATUS_BUKU', 'Dimusnahkan')
                ->where(function ($activeQuery) {
                    $activeQuery->whereNull('pemusnahan.IS_DELETE')
                        ->orWhere('pemusnahan.IS_DELETE', 0);
                })
                ->first();
    
            abort_unless($data, 404);
    
            return view('pustakawan.pemusnahan.berita_acara', [
                'data' => $data,
                'tanggalCetak' => Carbon::now('Asia/Jakarta'),
            ]);
        }
}

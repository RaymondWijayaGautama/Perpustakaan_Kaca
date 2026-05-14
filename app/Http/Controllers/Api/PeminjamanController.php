<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    private const ACTIVE_LOAN_STATUSES = ['Dipinjam', 'Terlambat'];
    private const RETURN_CONDITION_OPTIONS = ['Tersedia', 'Kembali', 'Rusak', 'Hilang', 'Nonaktif', 'Baik'];
    private const FINE_REQUIRED_CONDITIONS = ['Rusak', 'Hilang', 'Nonaktif'];

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

    private function ensurePustakawan(?string $nipKaryawan)
    {
        $petugas = $this->getPustakawan($nipKaryawan);

        if (!$petugas || strtolower((string) $petugas->JABATAN_FUNGSIONAL) !== 'pustakawan') {
            return response()->json([
                'message' => 'Hanya pustakawan yang dapat mengecek buku overdue.',
            ], 403);
        }

        return null;
    }

    private function parseBarcodeInput(?string $value): array
    {
        $raw = trim((string) $value);
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

    private function copyQuery()
    {
        return DB::table('cp_koleksi as copy')
            ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
            ->where('buku.IS_DELETE', 0)
            ->select(
                'copy.ID_CP_KOLEKSI as id_cp_koleksi',
                'copy.ISBN',
                'copy.STATUS_BUKU as status_buku',
                'buku.JUDUL_KOLEKSI as judul_koleksi'
            );
    }

    private function normalizeStatus(?string $status): string
    {
        return strtolower(trim((string) $status));
    }

    private function hasActiveLoan(int $idCpKoleksi): bool
    {
        return $this->activeLoanQuery()
            ->where('ID_CP_KOLEKSI', $idCpKoleksi)
            ->exists();
    }

    private function activeLoanQuery()
    {
        return DB::table('tr_peminjaman')
            ->whereNull('TGL_KEMBALI')
            ->whereIn('STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES);
    }

    private function normalizeDenda(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return max(0, (float) $value);
    }

    private function normalizeReturnCondition(?string $condition): string
    {
        $condition = trim((string) $condition);

        return in_array($condition, self::RETURN_CONDITION_OPTIONS, true) ? $condition : 'Tersedia';
    }

    private function conditionRequiresFine(string $condition): bool
    {
        return in_array($condition, self::FINE_REQUIRED_CONDITIONS, true);
    }

    private function resolvePeminjam(?string $identifier): ?object
    {
        $identifier = trim((string) $identifier);

        if ($identifier === '') {
            return null;
        }

        $siswa = DB::table('mst_siswa')
            ->where('IS_DELETE', 0)
            ->where(function ($query) use ($identifier) {
                $query->where('NISN_SISWA', $identifier);

                if (ctype_digit($identifier)) {
                    $query->orWhere('ID_SISWA_TETAP', (int) $identifier);
                }
            })
            ->select(
                'ID_SISWA_TETAP as id_siswa_tetap',
                'NISN_SISWA as identitas',
                'NAMA_SISWA_TETAP as nama',
                DB::raw("'siswa' as tipe")
            )
            ->first();

        if ($siswa) {
            return $siswa;
        }

        return DB::table('mst_karyawan')
            ->where('NIP_KARYAWAN', $identifier)
            ->where('IS_DELETE', 0)
            ->select(
                'NIP_KARYAWAN as nip_karyawan',
                'NIP_KARYAWAN as identitas',
                'NAMA_KARYAWAN as nama',
                'JABATAN_FUNGSIONAL as jabatan',
                DB::raw("'karyawan' as tipe")
            )
            ->first();
    }

    private function applyPeminjamFilter($query, object $peminjam)
    {
        if (($peminjam->tipe ?? '') === 'siswa') {
            return $query->where('tr_peminjaman.ID_SISWA_TETAP', $peminjam->id_siswa_tetap);
        }

        return $query->where('tr_peminjaman.NIP_KARYAWAN', $peminjam->nip_karyawan);
    }

    private function prepareBorrowableCopy(object $copy): object|string
    {
        if ($this->hasActiveLoan((int) $copy->id_cp_koleksi)) {
            return 'Buku ini sedang dipinjam atau belum diproses pengembaliannya.';
        }

        $status = $this->normalizeStatus($copy->status_buku);

        if (in_array($status, ['tersedia', 'kembali'], true)) {
            return $copy;
        }

        if ($status === 'dipinjam') {
            DB::table('cp_koleksi')
                ->where('ID_CP_KOLEKSI', $copy->id_cp_koleksi)
                ->update(['STATUS_BUKU' => 'Tersedia']);

            $copy->status_buku = 'Tersedia';

            return $copy;
        }

        return "Buku tidak bisa dipinjam karena status fisik saat ini: {$copy->status_buku}.";
    }

    private function resolveBorrowableCopy(array $barcode): object|array|null
    {
        if ($barcode['id_cp_koleksi']) {
            $query = $this->copyQuery()
                ->where('copy.ID_CP_KOLEKSI', $barcode['id_cp_koleksi']);

            if ($barcode['isbn']) {
                $query->where('copy.ISBN', $barcode['isbn']);
            }

            $copy = $query->first();

            if (!$copy) {
                return null;
            }

            $prepared = $this->prepareBorrowableCopy($copy);

            return is_string($prepared) ? ['message' => $prepared] : $prepared;
        }

        if (!$barcode['isbn']) {
            return ['message' => 'Format barcode tidak dikenali. Gunakan format ISBN/ID_COPY dari barcode buku.'];
        }

        $copies = $this->copyQuery()
            ->where('copy.ISBN', $barcode['isbn'])
            ->orderBy('copy.ID_CP_KOLEKSI')
            ->get();

        foreach ($copies as $copy) {
            $prepared = $this->prepareBorrowableCopy($copy);

            if (!is_string($prepared)) {
                return $prepared;
            }
        }

        return $copies->isEmpty()
            ? null
            : ['message' => 'Semua eksemplar buku ini sedang dipinjam atau tidak tersedia.'];
    }

    public function index(Request $request)
    {
        try {
            $query = DB::table('tr_peminjaman as peminjaman')
                ->leftJoin('mst_siswa as siswa', 'peminjaman.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
                ->leftJoin('mst_karyawan as karyawan', 'peminjaman.NIP_KARYAWAN', '=', 'karyawan.NIP_KARYAWAN')
                ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->select(
                    'peminjaman.ID_PEMINJAMAN as id_peminjaman',
                    'peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                    'peminjaman.ID_SISWA_TETAP as id_siswa_tetap',
                    'peminjaman.NIP_KARYAWAN as nip_karyawan',
                    'peminjaman.TGL_PINJAM as tgl_peminjaman',
                    'peminjaman.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                    'peminjaman.TGL_KEMBALI as tgl_kembali',
                    'peminjaman.STATUS_PEMINJAMAN as status_peminjaman',
                    'peminjaman.KONDISI_BUKU as kondisi_buku',
                    'peminjaman.KETERANGAN_PEMINJAMAN as keterangan_peminjaman',
                    'peminjaman.DENDA_PEMINJAMAN as denda_peminjaman',
                    DB::raw("COALESCE(siswa.NAMA_SISWA_TETAP, karyawan.NAMA_KARYAWAN, '-') as nama_peminjam"),
                    DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN, '-') as identitas_peminjam"),
                    DB::raw("CASE WHEN peminjaman.ID_SISWA_TETAP IS NOT NULL THEN 'Siswa' WHEN peminjaman.NIP_KARYAWAN IS NOT NULL THEN 'Karyawan' ELSE '-' END as tipe_peminjam"),
                    'copy.ISBN',
                    'copy.STATUS_BUKU as status_buku',
                    'buku.JUDUL_KOLEKSI as judul_buku'
                );

            if ($request->status && $request->status !== 'Semua') {
                $query->where('peminjaman.STATUS_PEMINJAMAN', $request->status);
            }

            return response()->json($query->orderBy('peminjaman.TGL_PINJAM', 'desc')->get());
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function overdue(Request $request)
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'min_hari_terlambat' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'search' => ['nullable', 'string', 'max:100'],
        ], [
            'editor_nip_karyawan.required' => 'Identitas pustakawan wajib dikirim.',
            'min_hari_terlambat.integer' => 'Batas hari harus berupa angka.',
            'min_hari_terlambat.min' => 'Batas hari minimal 1 hari.',
            'min_hari_terlambat.max' => 'Batas hari terlalu besar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($authError = $this->ensurePustakawan($request->editor_nip_karyawan)) {
            return $authError;
        }

        $minHariTerlambat = (int) $request->query('min_hari_terlambat', 30);
        $search = trim((string) $request->query('search', ''));
        $today = Carbon::now()->toDateString();

        $query = DB::table('tr_peminjaman as peminjaman')
            ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
            ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
            ->leftJoin('mst_siswa as siswa', 'peminjaman.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
            ->leftJoin('mst_karyawan as karyawan', 'peminjaman.NIP_KARYAWAN', '=', 'karyawan.NIP_KARYAWAN')
            ->whereNull('peminjaman.TGL_KEMBALI')
            ->whereIn('peminjaman.STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES)
            ->whereRaw('DATEDIFF(?, peminjaman.TGL_HARUS_KEMBALI) >= ?', [$today, $minHariTerlambat])
            ->select(
                'peminjaman.ID_PEMINJAMAN as id_peminjaman',
                'peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                'peminjaman.TGL_PINJAM as tgl_pinjam',
                'peminjaman.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                'peminjaman.STATUS_PEMINJAMAN as status_peminjaman',
                'peminjaman.KETERANGAN_PEMINJAMAN as keterangan_peminjaman',
                'peminjaman.NIP_KARYAWAN as nip_karyawan',
                'copy.ISBN',
                'copy.STATUS_BUKU as status_buku',
                'buku.JUDUL_KOLEKSI as judul_buku',
                DB::raw("COALESCE(siswa.NAMA_SISWA_TETAP, karyawan.NAMA_KARYAWAN, '-') as nama_peminjam"),
                DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN, '-') as identitas_peminjam"),
                DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN, '-') as nisn_siswa"),
                DB::raw("CASE WHEN peminjaman.ID_SISWA_TETAP IS NOT NULL THEN 'Siswa' WHEN peminjaman.NIP_KARYAWAN IS NOT NULL THEN 'Karyawan' ELSE '-' END as tipe_peminjam"),
                DB::raw("DATEDIFF('{$today}', peminjaman.TGL_HARUS_KEMBALI) as hari_terlambat")
            );

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                    ->orWhere('copy.ISBN', 'like', "%{$search}%")
                    ->orWhere('copy.ID_CP_KOLEKSI', 'like', "%{$search}%")
                    ->orWhere('peminjaman.ID_PEMINJAMAN', 'like', "%{$search}%")
                    ->orWhere('siswa.NAMA_SISWA_TETAP', 'like', "%{$search}%")
                    ->orWhere('karyawan.NAMA_KARYAWAN', 'like', "%{$search}%")
                    ->orWhere('siswa.NISN_SISWA', 'like', "%{$search}%")
                    ->orWhere('karyawan.NIP_KARYAWAN', 'like', "%{$search}%");
            });
        }

        $rows = $query
            ->orderByDesc('hari_terlambat')
            ->orderBy('buku.JUDUL_KOLEKSI')
            ->get();

        return response()->json([
            'status' => 'success',
            'filters' => [
                'min_hari_terlambat' => $minHariTerlambat,
                'tanggal_cek' => $today,
            ],
            'summary' => [
                'total' => $rows->count(),
                'maks_hari_terlambat' => (int) ($rows->max('hari_terlambat') ?? 0),
            ],
            'data' => $rows,
        ]);
    }

    public function katalogKoleksi(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = strtolower(trim((string) $request->query('status', 'semua')));
        $perPage = max(1, min((int) $request->query('per_page', 8), 50));

        $query = DB::table('cp_koleksi as copy')
            ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
            ->leftJoin('ref_koleksi as kategori', 'buku.ID_REF_KOLEKSI', '=', 'kategori.ID_REF_KOLEKSI')
            ->leftJoin('tr_peminjaman as pinjam_aktif', function ($join) {
                $join->on('pinjam_aktif.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                    ->whereNull('pinjam_aktif.TGL_KEMBALI')
                    ->whereIn('pinjam_aktif.STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES);
            })
            ->leftJoin('mst_siswa as siswa', 'pinjam_aktif.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
            ->leftJoin('mst_karyawan as karyawan', 'pinjam_aktif.NIP_KARYAWAN', '=', 'karyawan.NIP_KARYAWAN')
            ->where('buku.IS_DELETE', 0)
            ->select(
                'copy.ID_CP_KOLEKSI as id_cp_koleksi',
                'copy.ISBN',
                'copy.STATUS_BUKU as status_buku',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'buku.PENGARANG as pengarang',
                'buku.PENERBIT as penerbit',
                'buku.TAHUN as tahun',
                'buku.NO_RAK_BUKU as no_rak_buku',
                'kategori.DESKRIPSI_KATEGORI as kategori',
                'kategori.NO_KATEGORI_BUKU as kode_kategori',
                'pinjam_aktif.ID_PEMINJAMAN as id_peminjaman_aktif',
                'pinjam_aktif.TGL_PINJAM as tgl_pinjam',
                'pinjam_aktif.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                DB::raw("COALESCE(siswa.NAMA_SISWA_TETAP, karyawan.NAMA_KARYAWAN) as nama_peminjam"),
                DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN) as identitas_peminjam")
            );

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                    ->orWhere('buku.PENGARANG', 'like', "%{$search}%")
                    ->orWhere('buku.PENERBIT', 'like', "%{$search}%")
                    ->orWhere('buku.ISBN', 'like', "%{$search}%")
                    ->orWhere('copy.ID_CP_KOLEKSI', 'like', "%{$search}%")
                    ->orWhere('kategori.DESKRIPSI_KATEGORI', 'like', "%{$search}%");
            });
        }

        if ($status === 'tersedia') {
            $query->whereNull('pinjam_aktif.ID_PEMINJAMAN')
                ->whereIn(DB::raw('LOWER(copy.STATUS_BUKU)'), ['tersedia', 'kembali', 'dipinjam']);
        } elseif ($status === 'dipinjam') {
            $query->whereNotNull('pinjam_aktif.ID_PEMINJAMAN');
        } elseif ($status === 'tidak_tersedia') {
            $query->whereNull('pinjam_aktif.ID_PEMINJAMAN')
                ->whereNotIn(DB::raw('LOWER(copy.STATUS_BUKU)'), ['tersedia', 'kembali', 'dipinjam']);
        }

        $data = $query
            ->orderBy('buku.JUDUL_KOLEKSI')
            ->orderBy('copy.ID_CP_KOLEKSI')
            ->paginate($perPage);

        $data->getCollection()->transform(function ($item) {
            $statusFisik = strtolower(trim((string) $item->status_buku));
            $sedangDipinjam = $item->id_peminjaman_aktif !== null;
            $bisaDipinjam = !$sedangDipinjam && in_array($statusFisik, ['tersedia', 'kembali', 'dipinjam'], true);

            $item->barcode_pinjam = "{$item->ISBN}/{$item->id_cp_koleksi}";
            $item->jenis_koleksi = ((string) $item->kode_kategori) === '4' ? 'Laporan PKL' : ($item->kategori ?: 'Buku');
            $item->sedang_dipinjam = $sedangDipinjam;
            $item->bisa_dipinjam = $bisaDipinjam;
            $item->status_ketersediaan = $sedangDipinjam
                ? 'Sudah Dipinjam'
                : ($bisaDipinjam ? 'Belum Dipinjam' : 'Tidak Tersedia');

            return $item;
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $barcode = $this->parseBarcodeInput($request->input('isbn'));

        if ($barcode['raw'] === '') {
            return response()->json(['message' => 'Barcode atau ISBN buku wajib diisi.'], 422);
        }

        $bukuFisik = $this->resolveBorrowableCopy($barcode);

        if (!$bukuFisik) {
            return response()->json(['message' => 'Buku fisik tidak ditemukan atau tidak tersedia untuk dipinjam.'], 404);
        }

        if (is_array($bukuFisik)) {
            return response()->json(['message' => $bukuFisik['message']], 400);
        }

        $peminjamInput = $request->input('id_peminjam', $request->input('id_siswa_tetap'));
        $peminjam = $this->resolvePeminjam($peminjamInput);

        if (!$peminjam) {
            return response()->json(['message' => 'NISN/NIP peminjam tidak terdaftar.'], 404);
        }

        try {
            DB::beginTransaction();

            DB::table('tr_peminjaman')->insert([
                'ID_CP_KOLEKSI' => $bukuFisik->id_cp_koleksi,
                'ID_SISWA_TETAP' => ($peminjam->tipe ?? '') === 'siswa' ? $peminjam->id_siswa_tetap : null,
                'NIP_KARYAWAN' => ($peminjam->tipe ?? '') === 'karyawan' ? $peminjam->nip_karyawan : null,
                'TGL_PINJAM' => now(),
                'TGL_HARUS_KEMBALI' => now()->addDays(7),
                'STATUS_PEMINJAMAN' => 'Dipinjam',
                'KONDISI_BUKU' => 'Baik',
                'KETERANGAN_PEMINJAMAN' => '-',
                'DENDA_PEMINJAMAN' => 0,
            ]);

            DB::table('cp_koleksi')
                ->where('ID_CP_KOLEKSI', $bukuFisik->id_cp_koleksi)
                ->update(['STATUS_BUKU' => 'Dipinjam']);

            DB::commit();
            return response()->json(['message' => "Peminjaman berhasil dicatat untuk {$peminjam->nama}."]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal sistem: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_peminjaman' => 'required',
            'kondisi_buku' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $peminjamanLama = DB::table('tr_peminjaman')->where('ID_PEMINJAMAN', $id)->first();
            if (!$peminjamanLama) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            DB::table('tr_peminjaman')
                ->where('ID_PEMINJAMAN', $id)
                ->update(array_filter([
                    'STATUS_PEMINJAMAN' => $request->status_peminjaman,
                    'TGL_KEMBALI' => $request->status_peminjaman === 'Kembali'
                        ? ($peminjamanLama->TGL_KEMBALI ?: now()->toDateString())
                        : null,
                    'KONDISI_BUKU' => $request->kondisi_buku,
                    'KETERANGAN_PEMINJAMAN' => $request->keterangan ?? '-',
                ], fn ($value) => $value !== null || $request->status_peminjaman !== 'Kembali'));

            if ($request->status_peminjaman === 'Kembali') {
                DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $peminjamanLama->ID_CP_KOLEKSI)
                    ->update(['STATUS_BUKU' => 'Kembali']);
            } elseif (in_array($request->status_peminjaman, self::ACTIVE_LOAN_STATUSES, true)) {
                DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $peminjamanLama->ID_CP_KOLEKSI)
                    ->update(['STATUS_BUKU' => 'Dipinjam']);
            }

            DB::commit();
            return response()->json(['message' => 'Data peminjaman berhasil diperbarui!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $peminjaman = DB::table('tr_peminjaman')->where('ID_PEMINJAMAN', $id)->first();
            if (!$peminjaman) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            DB::table('tr_peminjaman')
                ->where('ID_PEMINJAMAN', $id)
                ->update([
                    'STATUS_PEMINJAMAN' => 'Dihapus',
                ]);

            if ($peminjaman->STATUS_PEMINJAMAN === 'Dipinjam') {
                DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $peminjaman->ID_CP_KOLEKSI)
                    ->update(['STATUS_BUKU' => 'Kembali']);
            }
            DB::commit();
            return response()->json(['message' => 'Data transaksi berhasil diarsipkan !']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus ' . $e->getMessage()], 500);
        }
    }

    public function cekAktif(Request $request)
    {
        $idMember = $request->id_member; // ID internal siswa/karyawan
        $inputBuku = $request->id_pinjam; // Input dari frontend (ISBN atau ID Peminjaman)
        $barcode = $this->parseBarcodeInput($inputBuku);
        $peminjam = $this->resolvePeminjam($idMember);

        if (!$peminjam) {
            return response()->json(['message' => 'NISN/NIP pemustaka tidak ditemukan.'], 404);
        }

        // Melakukan JOIN untuk melacak ISBN melalui cp_koleksi
        $query = DB::table('tr_peminjaman')
            ->join('cp_koleksi', 'tr_peminjaman.ID_CP_KOLEKSI', '=', 'cp_koleksi.ID_CP_KOLEKSI')
            ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
            ->whereNull('tr_peminjaman.TGL_KEMBALI') // Memastikan buku belum dikembalikan
            ->whereIn('tr_peminjaman.STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES)
            ->where(function ($query) use ($inputBuku, $barcode) {
                // Cek apakah input cocok dengan ISBN atau ID Transaksi atau ID Fisik Buku
                $query->where('cp_koleksi.ISBN', $inputBuku)
                      ->orWhere('tr_peminjaman.ID_PEMINJAMAN', $inputBuku)
                      ->orWhere('cp_koleksi.ID_CP_KOLEKSI', $inputBuku);

                if ($barcode['isbn']) {
                    $query->orWhere('cp_koleksi.ISBN', $barcode['isbn']);
                }

                if ($barcode['id_cp_koleksi']) {
                    $query->orWhere('cp_koleksi.ID_CP_KOLEKSI', $barcode['id_cp_koleksi']);
                }
            });

        $peminjaman = $this->applyPeminjamFilter($query, $peminjam)
            ->select(
                'tr_peminjaman.ID_PEMINJAMAN as id_peminjaman',
                'tr_peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                'tr_peminjaman.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                'mst_koleksi_buku.JUDUL_KOLEKSI as judul_koleksi',
                'cp_koleksi.ISBN'
            )
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Buku tidak ditemukan atau tidak sedang dipinjam oleh pemustaka ini.'], 404);
        }

        return response()->json($peminjaman);
    }

    public function batchReturn(Request $request)
    {
        $items = $request->input('items', []);

        if (!is_array($items) || count($items) === 0) {
            return response()->json(['message' => 'Daftar buku pengembalian masih kosong.'], 422);
        }

        // Panggil class kalkulator
        $kalkulator = new \App\Http\Controllers\Pustakawan\KalkulasiKeterlambatanPengembalian();;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $peminjaman = DB::table('tr_peminjaman')
                    ->where('ID_PEMINJAMAN', $item['id_peminjaman'] ?? null)
                    ->whereNull('TGL_KEMBALI')
                    ->whereIn('STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES)
                    ->lockForUpdate()
                    ->first();

                if (!$peminjaman) {
                    throw new \RuntimeException('Transaksi peminjaman aktif tidak ditemukan atau sudah pernah dikembalikan.');
                }

                // Eksekusi kalkulasi keterlambatan
                $hasilKalkulasi = $kalkulator->hitung($peminjaman->TGL_HARUS_KEMBALI, $item['tgl_kembali_manual'] ?? null);
                $denda = $this->normalizeDenda($item['denda'] ?? $item['denda_peminjaman'] ?? 0);

                $kondisiBuku = $this->normalizeReturnCondition($item['kondisi'] ?? 'Tersedia');
                $denda = isset($item['denda']) ? (float)$item['denda'] : 0;

                if ($this->conditionRequiresFine($kondisiBuku) && $denda <= 0) {
                    throw new \RuntimeException("Nominal denda wajib diisi untuk buku dengan kondisi: {$kondisiBuku}.");
                }

                // Update status di tabel transaksi
                $updated = DB::table('tr_peminjaman')
                    ->where('ID_PEMINJAMAN', $peminjaman->ID_PEMINJAMAN)
                    ->whereNull('TGL_KEMBALI')
                    ->update([
                        'TGL_KEMBALI' => $hasilKalkulasi['tgl_kembali'],
                        'STATUS_PEMINJAMAN' => 'Kembali',
                        'KONDISI_BUKU' => $kondisiBuku,
                        'KETERANGAN_PEMINJAMAN' => $hasilKalkulasi['keterangan'],
                        'DENDA_PEMINJAMAN' => $denda,
                    ]);

                if ($updated === 0) {
                    throw new \RuntimeException('Pengembalian gagal disimpan karena transaksi sudah berubah.');
                }

                // Update status fisik buku menjadi Kembali setelah pengembalian tercatat.
                DB::table('cp_koleksi')
                    ->where('ID_CP_KOLEKSI', $peminjaman->ID_CP_KOLEKSI)
                    ->update(['STATUS_BUKU' => 'Kembali']);
            }

            DB::commit();
            return response()->json(['message' => 'Proses pengembalian berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses data: ' . $e->getMessage()], 500);
        }
    }

    public function scanPengembalian(Request $request)
    {
        $inputBuku = $request->input('barcode', $request->input('id_pinjam', $request->input('isbn')));
        $barcode = $this->parseBarcodeInput($inputBuku);

        if ($barcode['raw'] === '') {
            return response()->json(['message' => 'Barcode buku wajib diisi.'], 422);
        }

        $peminjaman = DB::table('tr_peminjaman')
            ->join('cp_koleksi', 'tr_peminjaman.ID_CP_KOLEKSI', '=', 'cp_koleksi.ID_CP_KOLEKSI')
            ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
            ->leftJoin('mst_siswa', 'tr_peminjaman.ID_SISWA_TETAP', '=', 'mst_siswa.ID_SISWA_TETAP')
            ->leftJoin('mst_karyawan', 'tr_peminjaman.NIP_KARYAWAN', '=', 'mst_karyawan.NIP_KARYAWAN')
            ->whereNull('tr_peminjaman.TGL_KEMBALI')
            ->whereIn('tr_peminjaman.STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES)
            ->where(function ($query) use ($inputBuku, $barcode) {
                $query->where('cp_koleksi.ISBN', $inputBuku)
                    ->orWhere('tr_peminjaman.ID_PEMINJAMAN', $inputBuku)
                    ->orWhere('cp_koleksi.ID_CP_KOLEKSI', $inputBuku);

                if ($barcode['isbn']) {
                    $query->orWhere('cp_koleksi.ISBN', $barcode['isbn']);
                }

                if ($barcode['id_cp_koleksi']) {
                    $query->orWhere('cp_koleksi.ID_CP_KOLEKSI', $barcode['id_cp_koleksi']);
                }
            })
            ->select(
                'tr_peminjaman.ID_PEMINJAMAN as id_peminjaman',
                'tr_peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                'tr_peminjaman.ID_SISWA_TETAP as id_siswa_tetap',
                'tr_peminjaman.NIP_KARYAWAN as nip_karyawan',
                'tr_peminjaman.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                'mst_koleksi_buku.JUDUL_KOLEKSI as judul_koleksi',
                'cp_koleksi.ISBN',
                'mst_siswa.NISN_SISWA as nisn_siswa',
                'mst_karyawan.NIP_KARYAWAN as nip_peminjam',
                DB::raw("COALESCE(mst_siswa.NAMA_SISWA_TETAP, mst_karyawan.NAMA_KARYAWAN, '-') as nama_peminjam"),
                DB::raw("COALESCE(mst_siswa.NISN_SISWA, mst_karyawan.NIP_KARYAWAN, '-') as identitas_peminjam"),
                DB::raw("CASE WHEN tr_peminjaman.ID_SISWA_TETAP IS NOT NULL THEN 'Siswa' WHEN tr_peminjaman.NIP_KARYAWAN IS NOT NULL THEN 'Karyawan' ELSE '-' END as tipe_peminjam")
            )
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Buku tidak sedang dipinjam atau barcode tidak ditemukan.'], 404);
        }

        return response()->json($peminjaman);
    }

    public function prosesPengembalian(Request $request, $id)
    {
        $kalkulator = new \App\Http\Controllers\Pustakawan\KalkulasiKeterlambatanPengembalian();

        DB::beginTransaction();
        try {
            $peminjaman = DB::table('tr_peminjaman')
                ->where('ID_PEMINJAMAN', $id)
                ->whereNull('TGL_KEMBALI')
                ->whereIn('STATUS_PEMINJAMAN', self::ACTIVE_LOAN_STATUSES)
                ->lockForUpdate()
                ->first();

            if (!$peminjaman) {
                return response()->json(['message' => 'Data peminjaman aktif tidak ditemukan.'], 404);
            }

            $hasilKalkulasi = $kalkulator->hitung(
                $peminjaman->TGL_HARUS_KEMBALI,
                $request->input('tgl_kembali_manual')
            );
            $denda = $this->normalizeDenda($request->input('denda', $request->input('denda_peminjaman', 0)));

            $kondisiBuku = $this->normalizeReturnCondition($request->input('kondisi', 'Tersedia'));
            $denda = (float)$request->input('denda', 0);

            if ($this->conditionRequiresFine($kondisiBuku) && $denda <= 0) {
                return response()->json(['message' => "Nominal denda wajib diisi untuk kondisi buku: {$kondisiBuku}."], 422);
            }

            DB::table('tr_peminjaman')
                ->where('ID_PEMINJAMAN', $id)
                ->update([
                    'TGL_KEMBALI' => $hasilKalkulasi['tgl_kembali'],
                    'STATUS_PEMINJAMAN' => 'Kembali',
                    'KONDISI_BUKU' => $kondisiBuku,
                    'KETERANGAN_PEMINJAMAN' => $hasilKalkulasi['keterangan'],
                    'DENDA_PEMINJAMAN' => $denda,
                ]);

            DB::table('cp_koleksi')
                ->where('ID_CP_KOLEKSI', $peminjaman->ID_CP_KOLEKSI)
                ->update(['STATUS_BUKU' => 'Kembali']);

            DB::commit();
            return response()->json(['message' => 'Pengembalian buku berhasil diproses.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses pengembalian: ' . $e->getMessage()], 500);
        }
    }

    public function updatePengembalian(Request $request, $id)
    {
        $validator = validator($request->all(), [
            'tgl_kembali' => ['required', 'date'],
            'kondisi_buku_kembali' => ['required', 'string', 'max:25'],
            'denda' => ['nullable', 'numeric', 'min:0'],
            'keterangan_peminjaman' => ['nullable', 'string', 'max:255'],
        ], [
            'tgl_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tgl_kembali.date' => 'Tanggal kembali tidak valid.',
            'kondisi_buku_kembali.required' => 'Kondisi buku wajib diisi.',
            'denda.numeric' => 'Denda harus berupa angka.',
            'denda.min' => 'Denda tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $peminjaman = DB::table('tr_peminjaman')
                ->where('ID_PEMINJAMAN', $id)
                ->whereNotNull('TGL_KEMBALI')
                ->first();

            if (!$peminjaman) {
                return response()->json([
                    'message' => 'Data pengembalian tidak ditemukan atau belum diproses kembali.',
                ], 404);
            }

            $tglKembali = Carbon::parse($request->input('tgl_kembali'))->toDateString();
            $kondisiBuku = $this->normalizeReturnCondition($request->input('kondisi_buku_kembali'));
            $denda = $this->normalizeDenda($request->input('denda', 0));

            if ($this->conditionRequiresFine($kondisiBuku) && $denda <= 0) {
                return response()->json([
                    'message' => "Nominal denda wajib diisi untuk kondisi buku: {$kondisiBuku}.",
                ], 422);
            }

            DB::table('tr_peminjaman')
                ->where('ID_PEMINJAMAN', $id)
                ->update([
                    'TGL_KEMBALI' => $tglKembali,
                    'STATUS_PEMINJAMAN' => 'Kembali',
                    'KONDISI_BUKU' => $kondisiBuku,
                    'KETERANGAN_PEMINJAMAN' => $request->input('keterangan_peminjaman', $peminjaman->KETERANGAN_PEMINJAMAN ?? '-'),
                    'DENDA_PEMINJAMAN' => $denda,
                ]);

            $updated = DB::table('tr_peminjaman as peminjaman')
                ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
                ->leftJoin('mst_siswa as siswa', 'peminjaman.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
                ->leftJoin('mst_karyawan as karyawan', 'peminjaman.NIP_KARYAWAN', '=', 'karyawan.NIP_KARYAWAN')
                ->where('peminjaman.ID_PEMINJAMAN', $id)
                ->select(
                    'peminjaman.ID_PEMINJAMAN as id_peminjaman',
                    'peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                    'peminjaman.TGL_KEMBALI as tgl_kembali',
                    'peminjaman.KONDISI_BUKU as kondisi_buku_kembali',
                    'peminjaman.KETERANGAN_PEMINJAMAN as keterangan_peminjaman',
                    'peminjaman.DENDA_PEMINJAMAN as denda',
                    'buku.JUDUL_KOLEKSI as judul_koleksi',
                    'copy.ISBN',
                    DB::raw("COALESCE(siswa.NAMA_SISWA_TETAP, karyawan.NAMA_KARYAWAN, '-') as nama_peminjam"),
                    DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN, '-') as nisn_nip")
                )
                ->first();

            return response()->json([
                'message' => 'Data pengembalian berhasil diperbarui.',
                'data' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data pengembalian: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function historyPengembalian(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = DB::table('tr_peminjaman as peminjaman')
            ->join('cp_koleksi as copy', 'peminjaman.ID_CP_KOLEKSI', '=', 'copy.ID_CP_KOLEKSI')
            ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
            ->leftJoin('mst_siswa as siswa', 'peminjaman.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
            ->leftJoin('mst_karyawan as karyawan', 'peminjaman.NIP_KARYAWAN', '=', 'karyawan.NIP_KARYAWAN')
            ->whereNotNull('peminjaman.TGL_KEMBALI')
            ->select(
                'peminjaman.ID_PEMINJAMAN as id_peminjaman',
                'peminjaman.ID_CP_KOLEKSI as id_cp_koleksi',
                'peminjaman.TGL_KEMBALI as tgl_kembali',
                'peminjaman.KONDISI_BUKU as kondisi_buku_kembali',
                'peminjaman.KETERANGAN_PEMINJAMAN as keterangan_peminjaman',
                'peminjaman.DENDA_PEMINJAMAN as denda',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'copy.ISBN',
                DB::raw("COALESCE(siswa.NAMA_SISWA_TETAP, karyawan.NAMA_KARYAWAN, '-') as nama_peminjam"),
                DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN, '-') as nisn_nip")
            );

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('siswa.NAMA_SISWA_TETAP', 'like', "%{$search}%")
                    ->orWhere('karyawan.NAMA_KARYAWAN', 'like', "%{$search}%")
                    ->orWhere('siswa.NISN_SISWA', 'like', "%{$search}%")
                    ->orWhere('karyawan.NIP_KARYAWAN', 'like', "%{$search}%")
                    ->orWhere('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                    ->orWhere('copy.ISBN', 'like', "%{$search}%")
                    ->orWhere('peminjaman.ID_PEMINJAMAN', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->orderBy('peminjaman.TGL_KEMBALI', 'desc')->get(),
        ]);
    }

    /**
     * Mencari data peminjaman berdasarkan ID transaksi atau NISN/NIP pemustaka
     * untuk keperluan kalkulasi denda kerusakan.
     */
    public function cariPeminjamanDenda(Request $request)
    {
        $search = trim($request->input('search', ''));

        if ($search === '') {
            return response()->json(['message' => 'Masukkan ID Transaksi atau NISN/NIP pemustaka.'], 422);
        }

        $query = DB::table('tr_peminjaman as p')
            ->leftJoin('cp_koleksi as cp', 'p.ID_CP_KOLEKSI', '=', 'cp.ID_CP_KOLEKSI')
            ->leftJoin('mst_koleksi_buku as buku', 'cp.ISBN', '=', 'buku.ISBN')
            ->leftJoin('mst_siswa as siswa', 'p.ID_SISWA_TETAP', '=', 'siswa.ID_SISWA_TETAP')
            ->leftJoin('mst_karyawan as karyawan', 'p.NIP_KARYAWAN', '=', 'karyawan.NIP_KARYAWAN')
            ->select(
                'p.ID_PEMINJAMAN as id_peminjaman',
                'p.TGL_PINJAM as tgl_pinjam',
                'p.TGL_HARUS_KEMBALI as tgl_harus_kembali',
                'p.TGL_KEMBALI as tgl_kembali',
                'p.STATUS_PEMINJAMAN as status_peminjaman',
                'p.KONDISI_BUKU as kondisi_buku',
                'p.DENDA_PEMINJAMAN as denda_peminjaman',
                'buku.JUDUL_KOLEKSI as judul_koleksi',
                'cp.ISBN as isbn',
                'cp.ID_CP_KOLEKSI as id_cp_koleksi',
                DB::raw("COALESCE(siswa.NAMA_SISWA_TETAP, karyawan.NAMA_KARYAWAN) as nama_peminjam"),
                DB::raw("COALESCE(siswa.NISN_SISWA, karyawan.NIP_KARYAWAN) as identitas_peminjam")
            )
            ->where(function ($q) use ($search) {
                $q->where('p.ID_PEMINJAMAN', 'like', "%{$search}%")
                    ->orWhere('siswa.NISN_SISWA', 'like', "%{$search}%")
                    ->orWhere('karyawan.NIP_KARYAWAN', 'like', "%{$search}%")
                    ->orWhere('siswa.NAMA_SISWA_TETAP', 'like', "%{$search}%")
                    ->orWhere('karyawan.NAMA_KARYAWAN', 'like', "%{$search}%");
            })
            ->orderBy('p.TGL_PINJAM', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $query,
        ]);
    }

    /**
     * Menyimpan denda kerusakan buku untuk transaksi tertentu.
     */
    public function simpanDendaKerusakan(Request $request)
    {
        $request->validate([
            'id_peminjaman' => 'required',
            'deskripsi_kerusakan' => 'required|string|max:255',
            'nominal_denda' => 'required|numeric|min:1',
        ]);

        $peminjaman = DB::table('tr_peminjaman')
            ->where('ID_PEMINJAMAN', $request->id_peminjaman)
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Transaksi peminjaman tidak ditemukan.'], 404);
        }

        DB::table('tr_peminjaman')
            ->where('ID_PEMINJAMAN', $request->id_peminjaman)
            ->update([
                'KONDISI_BUKU' => $request->deskripsi_kerusakan,
                'DENDA_PEMINJAMAN' => $request->nominal_denda,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Denda kerusakan berhasil dicatat.',
        ]);
    }
}

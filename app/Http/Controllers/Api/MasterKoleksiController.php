<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterKoleksiController extends Controller
{
    private function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka.',
            'max' => ':attribute maksimal :max karakter.',
            'regex' => ':attribute hanya boleh berisi huruf, angka, dan tanda hubung.',
            'unique' => ':attribute sudah digunakan.',
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'editor_nip_karyawan' => 'Identitas pustakawan',
            'kode_kategori' => 'Kode kategori',
            'deskripsi_kategori' => 'Deskripsi kategori',
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
                'message' => 'Hanya pustakawan yang dapat mengelola master koleksi.',
            ], 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $sortBy = $request->query('sort_by', 'deskripsi');
        $sortOrder = strtolower((string) $request->query('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $allowedSort = [
            'id_ref_koleksi' => 'ID_REF_KOLEKSI',
            'kode' => 'NO_KATEGORI_BUKU',
            'deskripsi' => 'DESKRIPSI_KATEGORI',
        ];

        $query = DB::table('ref_koleksi')
            ->where('IS_DELETE', 0)
            ->where('ID_REF_KOLEKSI', '!=', 4);

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('NO_KATEGORI_BUKU', 'like', "%{$search}%")
                    ->orWhere('DESKRIPSI_KATEGORI', 'like', "%{$search}%")
                    ->orWhere('ID_REF_KOLEKSI', 'like', "%{$search}%");
            });
        }

        $data = $query
            ->orderBy($allowedSort[$sortBy] ?? $allowedSort['deskripsi'], $sortOrder)
            ->paginate($perPage, [
                'ID_REF_KOLEKSI as id_ref_koleksi',
                'NO_KATEGORI_BUKU as kode_kategori',
                'DESKRIPSI_KATEGORI as deskripsi_kategori',
            ]);

        return response()->json($data);
    }

    public function options(): JsonResponse
    {
        $kategori = DB::table('ref_koleksi')
            ->where('IS_DELETE', 0)
            ->where('ID_REF_KOLEKSI', '!=', 4)
            ->orderBy('DESKRIPSI_KATEGORI')
            ->get([
                'ID_REF_KOLEKSI as id_ref_koleksi',
                'NO_KATEGORI_BUKU as kode_kategori',
                'DESKRIPSI_KATEGORI as deskripsi',
            ]);

        return response()->json($kategori);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'kode_kategori' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Za-z0-9\\-]+$/',
                Rule::unique('ref_koleksi', 'NO_KATEGORI_BUKU'),
            ],
            'deskripsi_kategori' => ['required', 'string', 'max:100'],
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

        $id = DB::table('ref_koleksi')->insertGetId([
            'NO_KATEGORI_BUKU' => strtoupper(trim((string) $request->kode_kategori)),
            'DESKRIPSI_KATEGORI' => trim((string) $request->deskripsi_kategori),
            'IS_DELETE' => 0,
        ], 'ID_REF_KOLEKSI');

        $koleksi = DB::table('ref_koleksi')
            ->where('ID_REF_KOLEKSI', $id)
            ->first([
                'ID_REF_KOLEKSI as id_ref_koleksi',
                'NO_KATEGORI_BUKU as kode_kategori',
                'DESKRIPSI_KATEGORI as deskripsi_kategori',
            ]);

        return response()->json([
            'message' => 'Master koleksi berhasil ditambahkan.',
            'data' => $koleksi,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if ($id === 4) {
            return response()->json([
                'message' => 'Kategori laporan PKL dikunci oleh sistem dan tidak dapat diubah.',
            ], 403);
        }

        $validator = validator($request->all(), [
            'editor_nip_karyawan' => ['required', 'string', 'max:20'],
            'kode_kategori' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Za-z0-9\\-]+$/',
                Rule::unique('ref_koleksi', 'NO_KATEGORI_BUKU')->ignore($id, 'ID_REF_KOLEKSI'),
            ],
            'deskripsi_kategori' => ['required', 'string', 'max:100'],
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

        $existing = DB::table('ref_koleksi')
            ->where('ID_REF_KOLEKSI', $id)
            ->where('IS_DELETE', 0)
            ->first();

        if (!$existing) {
            return response()->json([
                'message' => 'Master koleksi tidak ditemukan.',
            ], 404);
        }

        DB::table('ref_koleksi')
            ->where('ID_REF_KOLEKSI', $id)
            ->update([
                'NO_KATEGORI_BUKU' => strtoupper(trim((string) $request->kode_kategori)),
                'DESKRIPSI_KATEGORI' => trim((string) $request->deskripsi_kategori),
            ]);

        $koleksi = DB::table('ref_koleksi')
            ->where('ID_REF_KOLEKSI', $id)
            ->first([
                'ID_REF_KOLEKSI as id_ref_koleksi',
                'NO_KATEGORI_BUKU as kode_kategori',
                'DESKRIPSI_KATEGORI as deskripsi_kategori',
            ]);

        return response()->json([
            'message' => 'Master koleksi berhasil diperbarui.',
            'data' => $koleksi,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if ($id === 4) {
            return response()->json([
                'message' => 'Kategori laporan PKL dikunci oleh sistem dan tidak dapat dihapus.',
            ], 403);
        }

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

        $existing = DB::table('ref_koleksi')
            ->where('ID_REF_KOLEKSI', $id)
            ->where('IS_DELETE', 0)
            ->first();

        if (!$existing) {
            return response()->json([
                'message' => 'Master koleksi tidak ditemukan.',
            ], 404);
        }

        $dipakaiBuku = DB::table('mst_koleksi_buku')
            ->where('ID_REF_KOLEKSI', $id)
            ->where('IS_DELETE', 0)
            ->exists();

        if ($dipakaiBuku) {
            return response()->json([
                'message' => 'Master koleksi tidak bisa dihapus karena masih dipakai oleh data buku aktif.',
            ], 409);
        }

        DB::table('ref_koleksi')
            ->where('ID_REF_KOLEKSI', $id)
            ->update([
                'IS_DELETE' => 1,
            ]);

        return response()->json([
            'message' => 'Master koleksi berhasil dihapus.',
        ]);
    }
}

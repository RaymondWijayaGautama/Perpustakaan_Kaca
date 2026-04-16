<?php
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Log;

    class PeminjamanController extends Controller
    {
        public function index(Request $request)
        {
            try {
                // Kita mulai join dari tabel laporan ke buku via cp_koleksi
                $query = DB::table('mst_koleksi_laporan')
                    ->join('cp_koleksi', 'mst_koleksi_laporan.id_mst_laporan', '=', 'cp_koleksi.id_mst_laporan')
                    ->join('mst_koleksi_buku', 'cp_koleksi.ISBN', '=', 'mst_koleksi_buku.ISBN')
                    ->where('mst_koleksi_laporan.is_delete', 0)
                    ->select(
                        'mst_koleksi_laporan.id_mst_laporan',
                        'mst_koleksi_buku.judul_koleksi',
                        'mst_koleksi_buku.pengarang as nama_siswa_tetap', // Alias agar cocok dengan UI React kamu
                        'mst_koleksi_buku.tahun',
                        'mst_koleksi_buku.ISBN'
                    );

                // Filter Tahun (Hanya jalan jika input tidak kosong)
                if ($request->filled('tahun')) {
                    $query->where('mst_koleksi_buku.tahun', $request->tahun);
                }

                // Filter Penulis/Siswa
                if ($request->filled('penulis')) {
                    $query->where('mst_koleksi_buku.pengarang', 'like', '%' . $request->penulis . '%');
                }

                // Gunakan pagination sesuai request dari React (default 10)
                $perPage = $request->get('per_page', 10);
                $data = $query->paginate($perPage);

                return response()->json($data);

            } catch (\Exception $e) {
                // Ini akan membantu kamu melihat error spesifik di Response Preview Chrome
                return response()->json([
                    'message' => 'Gagal memuat laporan PKL',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        public function store(Request $request)
        {

            $hasil_scan = trim($request->isbn); 
            $pecah = explode('-', $hasil_scan);
            if (count($pecah) < 2) {
                return response()->json(['message' => 'Gagal Format Barcode salah ! Harus mengandung ID.'], 400);
            }
            
            $id_fisik = array_pop($pecah);      
            $isbn_murni = implode('-', $pecah); 
            $bukuFisik = DB::table('cp_koleksi')
                ->where('id_cp_koleksi', $id_fisik)
                ->where('ISBN', $isbn_murni) 
                ->first();
                
            if (!$bukuFisik) {
                return response()->json(['message' => "Gagal Buku ID '$id_fisik' & ISBN '$isbn_murni' tidak ada di database!"], 404);
            }

            if ($bukuFisik->status_buku !== 'Tersedia') {
                return response()->json(['message' => "Gagal ! Buku ini sedang dipinjam !"], 400);
            }

            $siswa = DB::table('mst_siswa')->where('nisn_siswa', $request->id_siswa_tetap)->first();
                
            if (!$siswa) {
                return response()->json(['message' => 'Gagal Siswa dengan NISN tersebut tidak terdaftar!'], 404);
            }

            try {
                DB::beginTransaction();

                DB::table('tr_peminjaman')->insert([
                    'id_cp_koleksi'         => $bukuFisik->id_cp_koleksi,
                    'id_siswa_tetap'        => $siswa->id_siswa_tetap, 
                    'nip_karyawan'          => $request->nip_karyawan,
                    'tgl_peminjaman'        => now(),
                    'tgl_harus_kembali'     => now()->addDays(7), 
                    'status_peminjaman'     => 'Dipinjam',
                    'kondisi_buku'          => 'Baik',  
                    'keterangan_peminjaman' => '-',   
                    'denda_peminjaman'      => 0        
                ]);

                DB::table('cp_koleksi')
                    ->where('id_cp_koleksi', $bukuFisik->id_cp_koleksi)
                    ->update(['status_buku' => 'Dipinjam']);

                DB::commit();
                return response()->json(['message' => 'Peminjaman berhasil dicatat!']);

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

                $peminjamanLama = DB::table('tr_peminjaman')->where('id_peminjaman', $id)->first();
                if (!$peminjamanLama) {
                    return response()->json(['message' => 'Data tidak ditemukan'], 404);
                }

                DB::table('tr_peminjaman')
                    ->where('id_peminjaman', $id)
                    ->update([
                        'status_peminjaman'     => $request->status_peminjaman,
                        'kondisi_buku'          => $request->kondisi_buku,
                        'keterangan_peminjaman' => $request->keterangan ?? '-',
                        'updated_at'            => now()
                    ]);

                if ($request->status_peminjaman === 'Kembali') {
                    DB::table('cp_koleksi')
                        ->where('id_cp_koleksi', $peminjamanLama->id_cp_koleksi)
                        ->update(['status_buku' => 'Tersedia']);
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
                $peminjaman = DB::table('tr_peminjaman')->where('id_peminjaman', $id)->first();
                if (!$peminjaman) {
                    return response()->json(['message' => 'Data tidak ditemukan'], 404);
                }

                DB::table('tr_peminjaman')
                    ->where('id_peminjaman', $id)
                    ->update([
                        'status_peminjaman' => 'Dihapus', 
                        'updated_at'        => now()
                    ]);

                if ($peminjaman->status_peminjaman === 'Dipinjam') {
                    DB::table('cp_koleksi')
                        ->where('id_cp_koleksi', $peminjaman->id_cp_koleksi)
                        ->update(['status_buku' => 'Tersedia']);
                }
                DB::commit();
                return response()->json(['message' => 'Data transaksi berhasil diarsipkan !']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Gagal menghapus ' . $e->getMessage()], 500);
            }
        }

        public function getAnggota(Request $request)
        {
            try {
                $search = $request->query('search');
                $role = $request->query('role');
                $perPage = $request->get('per_page', 10);

                // 1. Query Siswa
                $siswaQuery = DB::table('mst_siswa')
                    ->where('is_delete', 0)
                    ->select('id_siswa_tetap as id_internal', 'nisn_siswa as identitas', 'nama_siswa_tetap as nama', DB::raw("'Siswa' as role"));

                // 2. Query Karyawan
                $karyawanQuery = DB::table('mst_karyawan')
                    ->where('is_delete', 0)
                    ->select('nip_karyawan as id_internal', 'nip_karyawan as identitas', 'nama_karyawan as nama', DB::raw("'Karyawan' as role"));

                // Gabungkan
                $combined = $karyawanQuery->union($siswaQuery);

                // 3. Bungkus Union dalam Query Baru untuk Filtering
                $finalQuery = DB::table(DB::raw("({$combined->toSql()}) as combined"))
                    ->mergeBindings($combined); // Penting agar parameter union tidak hilang

                // Filter Nama
                if ($search) {
                    $finalQuery->where('nama', 'like', "%{$search}%");
                }

                // Filter Role
                if ($role && $role !== 'Semua') {
                    $finalQuery->where('role', $role);
                }

                $results = $finalQuery->orderBy('nama', 'asc')->paginate($perPage);

                return response()->json($results);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }
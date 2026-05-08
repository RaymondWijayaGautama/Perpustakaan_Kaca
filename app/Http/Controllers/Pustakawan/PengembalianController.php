<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PengembalianController extends Controller
{
    public function prosesKembali(Request $request)
    {
        // Validasi Role Karyawan
        if ($request->role !== 'karyawan') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $scannedCode = $request->id_peminjaman; // Ini bisa berisi ISBN hasil scan

        // Mencari peminjaman aktif berdasarkan ID Transaksi ATAU ISBN Buku
        $peminjaman = DB::table('tr_peminjaman')
            ->join('cp_koleksi', 'tr_peminjaman.id_cp_koleksi', '=', 'cp_koleksi.id_cp_koleksi')
            ->where(function($query) use ($scannedCode) {
                $query->where('tr_peminjaman.id_tr_peminjaman', $scannedCode)
                      ->orWhere('cp_koleksi.ISBN', $scannedCode);
            })
            ->whereNull('tr_peminjaman.tgl_kembali')
            ->select('tr_peminjaman.*', 'cp_koleksi.id_cp_koleksi')
            ->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Buku dengan ISBN ini tidak sedang dipinjam.'], 404);
        }

        DB::beginTransaction();
        try {
            // Update transaksi peminjaman 
            DB::table('tr_peminjaman')
                ->where('id_tr_peminjaman', $peminjaman->id_tr_peminjaman)
                ->update([
                    'tgl_kembali' => Carbon::now()->toDateString(),
                    'status_peminjaman' => 'Kembali'
                ]);

            // Update status fisik buku menjadi Kembali setelah transaksi selesai.
            DB::table('cp_koleksi')
                ->where('id_cp_koleksi', $peminjaman->id_cp_koleksi)
                ->update(['status_buku' => 'Kembali']);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Buku berhasil dikembalikan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui data.'], 500);
        }
    }

   public function history(Request $request)
    {
        try {
            $query = DB::table('tr_peminjaman as tp')
                ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.ID_CP_KOLEKSI')
                ->join('mst_koleksi_buku as buku', 'ck.ISBN', '=', 'buku.ISBN')
                ->leftJoin('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.ID_SISWA_TETAP')
                ->leftJoin('mst_karyawan as mk', 'tp.NIP_KARYAWAN', '=', 'mk.NIP_KARYAWAN')
                // KITA UBAH DISINI: Ambil yang TGL_KEMBALI ada isinya ATAU Statusnya 'Dikembalikan'
                ->where(function($q) {
                    $q->whereNotNull('tp.TGL_KEMBALI')
                      ->orWhere('tp.STATUS_PEMINJAMAN', 'Dikembalikan');
                })
                ->select(
                    'tp.ID_PEMINJAMAN',
                    'tp.TGL_KEMBALI',
                    'tp.STATUS_PEMINJAMAN',
                    'ms.NAMA_SISWA_TETAP',
                    'mk.NAMA_KARYAWAN',
                    'ms.NISN_SISWA',
                    'mk.NIP_KARYAWAN',
                    'buku.JUDUL_KOLEKSI',
                    'tp.DENDA_PEMINJAMAN',
                    'tp.KONDISI_BUKU'
                );

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->where('ms.NAMA_SISWA_TETAP', 'like', "%{$search}%")
                      ->orWhere('mk.NAMA_KARYAWAN', 'like', "%{$search}%")
                      ->orWhere('ms.NISN_SISWA', 'like', "%{$search}%")
                      ->orWhere('mk.NIP_KARYAWAN', 'like', "%{$search}%")
                      ->orWhere('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                      ->orWhere('tp.ID_PEMINJAMAN', 'like', "%{$search}%");
                });
            }

            $riwayat = $query->orderBy('tp.ID_PEMINJAMAN', 'desc')->get();

            $formattedData = $riwayat->map(function ($item) {
                return [
                    'id_peminjaman' => $item->ID_PEMINJAMAN,
                    // Jika TGL_KEMBALI null tapi status Dikembalikan, kita beri tanda
                    'tgl_kembali' => $item->TGL_KEMBALI ?? 'Proses Kembali',
                    'nama_peminjam' => $item->NAMA_SISWA_TETAP ?? $item->NAMA_KARYAWAN ?? '-',
                    'nisn_nip' => $item->NISN_SISWA ?? $item->NIP_KARYAWAN ?? '-',
                    'judul_koleksi' => $item->JUDUL_KOLEKSI,
                    'denda' => $item->DENDA_PEMINJAMAN ?? 0,
                    'kondisi_buku_kembali' => $item->KONDISI_BUKU ?? 'Baik'
                ];
            });

            return response()->json(['status' => 'success', 'data' => $formattedData]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $kelasTerakhir = DB::table('hst_kelas as hk')
            ->joinSub(
                DB::table('hst_kelas')
                    ->select('ID_SISWA_TETAP', DB::raw('MAX(ID_HST_KELAS) as ID_HST_KELAS'))
                    ->groupBy('ID_SISWA_TETAP'),
                'latest_hk',
                function ($join) {
                    $join->on('hk.ID_HST_KELAS', '=', 'latest_hk.ID_HST_KELAS');
                }
            )
            ->select('hk.ID_SISWA_TETAP', 'hk.KELAS');

        $query = DB::table('tr_peminjaman as tp')
            ->join('cp_koleksi as ck', 'tp.ID_CP_KOLEKSI', '=', 'ck.ID_CP_KOLEKSI')
            ->join('mst_koleksi_buku as buku', 'ck.ISBN', '=', 'buku.ISBN')
            ->leftJoin('mst_siswa as ms', 'tp.ID_SISWA_TETAP', '=', 'ms.ID_SISWA_TETAP')
            ->leftJoin('mst_karyawan as mk', 'tp.NIP_KARYAWAN', '=', 'mk.NIP_KARYAWAN')
            ->leftJoinSub($kelasTerakhir, 'kelas_terakhir', function ($join) {
                $join->on('ms.ID_SISWA_TETAP', '=', 'kelas_terakhir.ID_SISWA_TETAP');
            })
            ->where(function($q) {
                $q->whereNotNull('tp.TGL_KEMBALI')
                  ->orWhere('tp.STATUS_PEMINJAMAN', 'Dikembalikan');
            })
            ->select(
                'tp.ID_PEMINJAMAN',
                'tp.ID_CP_KOLEKSI',
                'tp.TGL_PINJAM',
                'tp.TGL_HARUS_KEMBALI',
                'tp.TGL_KEMBALI',
                'tp.STATUS_PEMINJAMAN',
                'ms.NAMA_SISWA_TETAP',
                'mk.NAMA_KARYAWAN',
                'ms.NISN_SISWA',
                'mk.NIP_KARYAWAN',
                'mk.JABATAN_FUNGSIONAL',
                'kelas_terakhir.KELAS',
                'ck.ISBN',
                'buku.JUDUL_KOLEKSI',
                'tp.DENDA_PEMINJAMAN',
                'tp.KONDISI_BUKU'
            );

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('ms.NAMA_SISWA_TETAP', 'like', "%{$search}%")
                  ->orWhere('mk.NAMA_KARYAWAN', 'like', "%{$search}%")
                  ->orWhere('ms.NISN_SISWA', 'like', "%{$search}%")
                  ->orWhere('mk.NIP_KARYAWAN', 'like', "%{$search}%")
                  ->orWhere('buku.JUDUL_KOLEKSI', 'like', "%{$search}%")
                  ->orWhere('ck.ISBN', 'like', "%{$search}%")
                  ->orWhere('ck.ID_CP_KOLEKSI', 'like', "%{$search}%")
                  ->orWhere('tp.ID_PEMINJAMAN', 'like', "%{$search}%");
            });
        }

        $riwayat = $query->orderBy('tp.ID_PEMINJAMAN', 'desc')->get();

        $templatePath = resource_path('templates/TemplatePengembalian.xlsx');

        if (!is_file($templatePath)) {
            $templatePath = storage_path('app/TemplatePengembalian.xlsx');
        }

        if (!is_file($templatePath)) {
            abort(500, 'Template export pengembalian tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $this->fillPengembalianTemplate($sheet, $riwayat);

        $fileName = 'riwayat_pengembalian_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function fillPengembalianTemplate(Worksheet $sheet, $riwayat): void
    {
        $startRow = 5;
        $dateColumns = ['H', 'I', 'J'];

        foreach ($riwayat as $index => $item) {
            $row = $startRow + $index;
            $isSiswa = !empty($item->NAMA_SISWA_TETAP);
            $tglKembali = $item->TGL_KEMBALI ? Carbon::parse($item->TGL_KEMBALI) : null;
            $tglTempo = $item->TGL_HARUS_KEMBALI ? Carbon::parse($item->TGL_HARUS_KEMBALI) : null;
            $terlambat = ($tglKembali && $tglTempo && $tglKembali->greaterThan($tglTempo))
                ? $tglTempo->diffInDays($tglKembali)
                : 0;

            $sheet->setCellValue("B{$row}", $index + 1);
            $sheet->setCellValue("C{$row}", $item->NAMA_SISWA_TETAP ?? $item->NAMA_KARYAWAN ?? '-');
            $sheet->setCellValue("D{$row}", $isSiswa ? 'Siswa' : 'Karyawan');
            $sheet->setCellValue("E{$row}", $isSiswa ? ($item->KELAS ?? '-') : ($item->JABATAN_FUNGSIONAL ?? '-'));
            $sheet->setCellValue("F{$row}", $item->JUDUL_KOLEKSI ?? '-');
            $sheet->setCellValue("G{$row}", trim(($item->ISBN ?? '-') . '/' . ($item->ID_CP_KOLEKSI ?? '-')));
            $sheet->setCellValue("K{$row}", $terlambat);
            $sheet->setCellValue("L{$row}", $item->KONDISI_BUKU ?? 'Baik');
            $sheet->setCellValue("M{$row}", (float) ($item->DENDA_PEMINJAMAN ?? 0));
            $sheet->setCellValue("N{$row}", '');

            $this->setExcelDate($sheet, "H{$row}", $item->TGL_PINJAM);
            $this->setExcelDate($sheet, "I{$row}", $item->TGL_HARUS_KEMBALI);
            $this->setExcelDate($sheet, "J{$row}", $item->TGL_KEMBALI);
        }

        if ($riwayat->isNotEmpty()) {
            $lastRow = $startRow + $riwayat->count() - 1;

            $sheet->getStyle("B{$startRow}:N{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            $sheet->getStyle("B{$startRow}:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$startRow}:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$startRow}:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M{$startRow}:M{$lastRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            foreach ($dateColumns as $column) {
                $sheet->getStyle("{$column}{$startRow}:{$column}{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            }
        }

        foreach (range('B', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function setExcelDate(Worksheet $sheet, string $cell, $value): void
    {
        if (!$value) {
            $sheet->setCellValue($cell, '-');
            return;
        }

        $sheet->setCellValue($cell, ExcelDate::PHPToExcel(Carbon::parse($value)));
    }
}

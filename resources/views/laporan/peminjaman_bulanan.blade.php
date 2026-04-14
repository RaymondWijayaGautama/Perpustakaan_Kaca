<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Bulanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-header { background-color: #4e73df; border-bottom: none; }
        .table thead { background-color: #f8f9fc; }
        .badge-count { background-color: #4e73df; font-weight: 500; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0">
            <div class="card-header text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i> Laporan Jumlah Peminjaman Per Bulan</h5>
                <a href="{{ url('/laporan/peminjaman-bulanan/pdf') }}" class="btn btn-danger btn-sm shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">No</th>
                                <th width="50%">Bulan</th>
                                <th width="40%">Total Peminjaman</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanPeminjaman as $index => $data)
                            <tr>
                                <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="text-capitalize text-dark fw-semibold text-start ps-5">{{ $data->bulan }}</td>
                                <td>
                                    <span class="badge badge-count px-3 py-2 fs-6">
                                        {{ $data->total_peminjaman }} Transaksi
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted italic">
                                    <i class="fas fa-info-circle me-1"></i> Belum ada data peminjaman di tahun {{ $tahun }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-muted small py-3">
                <i class="fas fa-calendar-check me-1"></i> Rekapitulasi Data Tahun {{ $tahun }}
            </div>
        </div>
    </div>
</body>
</html>
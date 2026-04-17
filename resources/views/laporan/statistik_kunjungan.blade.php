<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Statistik Kunjungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-header { background-color: #4e73df; }
        .badge-stat { background-color: #4e73df; color: white; border-radius: 20px; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0">
            <div class="card-header text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i> Statistik Kunjungan Bulanan</h5>
                <a href="{{ url('/laporan/statistik-kunjungan/pdf') }}" class="btn btn-danger btn-sm shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
            <div class="card-body">
                <table class="table table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Urutan</th>
                            <th>Bulan Kunjungan</th>
                            <th>Jumlah Pengunjung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanKunjungan as $data)
                        <tr>
                            <td>{{ $data->urutan_bulan }}</td>
                            <td class="text-capitalize fw-semibold">{{ $data->bulan }}</td>
                            <td><span class="badge badge-stat px-4 py-2">{{ $data->total_kunjungan }} Orang</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-4 text-muted">Data kunjungan tahun {{ $tahun }} belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
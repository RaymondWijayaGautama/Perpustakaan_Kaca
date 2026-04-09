<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kunjungan Bulanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">Laporan Jumlah Kunjungan Per Bulan</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Total Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanKunjungan as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-capitalize">{{ $data->bulan }}</td>
                            <td><span class="badge bg-primary fs-6">{{ $data->total_kunjungan }} Pengunjung</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted">Belum ada data kunjungan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
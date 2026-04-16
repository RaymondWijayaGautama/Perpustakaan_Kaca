<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Terpopuler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-header { background-color: #4e73df; border-bottom: none; }
        .badge-count { background-color: #4e73df; font-weight: 500; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0">
            <div class="card-header text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-fire me-2"></i> Daftar Buku Paling Sering Dipinjam</h5>
                <a href="{{ url('/laporan/buku-terpopuler/pdf') }}" class="btn btn-danger btn-sm shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">Rank</th>
                            <th width="40%">Judul Buku</th>
                            <th width="25%">Pengarang</th>
                            <th width="25%">Total Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanBuku as $index => $data)
                        <tr>
                            <td class="fw-bold text-muted">#{{ $index + 1 }}</td>
                            <td class="text-dark fw-semibold text-start">{{ $data->judul_koleksi }}</td>
                            <td class="text-muted small text-start">{{ $data->pengarang }}</td>
                            <td>
                                <span class="badge badge-count px-3 py-2 fs-6">
                                    {{ $data->total_dipinjam }} Transaksi
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-muted italic">Belum ada data peminjaman buku tahun ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-muted small py-3">
                <i class="fas fa-info-circle me-1"></i> Menampilkan data buku terpopuler sepanjang tahun {{ $tahun }}
            </div>
        </div>
    </div>
</body>
</html>
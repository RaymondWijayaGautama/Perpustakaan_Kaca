<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 10 Siswa Terajin - Wigaty Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Laporan Top 10 Siswa Terajin Meminjam Buku</h4>
            <a href="/laporan/siswa-terajin/pdf" class="btn btn-danger btn-sm" target="_blank">
                Export PDF
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">Peringkat</th>
                        <th width="35%">Nama Siswa</th>
                        <th width="20%">NIS</th>
                        <th width="20%">Kelas</th>
                        <th width="20%">Total Meminjam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaTerajin as $index => $siswa)
                    <tr>
                        <td>
                            @if($index == 0) <span class="badge bg-warning text-dark fs-6">🥇 1</span>
                            @elseif($index == 1) <span class="badge bg-secondary fs-6">🥈 2</span>
                            @elseif($index == 2) <span class="badge" style="background-color: #cd7f32; font-size: 1rem;">🥉 3</span>
                            @else <strong>{{ $index + 1 }}</strong>
                            @endif
                        </td>
                        <td class="text-start">{{ $siswa->nama_siswa_tetap ?? 'Nama Tidak Ada' }}</td>
                        <td>{{ $siswa->nisn_siswa ?? '-' }}</td>
                        <td>{{ $siswa->kelas ?? '-' }}</td>
                        <td>
                            <span class="badge bg-success fs-6">
                                {{ $siswa->peminjaman_count }} Buku
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-muted py-4">Belum ada data peminjaman di sistem.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
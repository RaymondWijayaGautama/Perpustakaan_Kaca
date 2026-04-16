<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Koleksi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #275f9c);
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="container py-5">

    <div class="card shadow p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>📚 Data Koleksi Buku</h3>

            <a href="{{ route('koleksi.create') }}" class="btn btn-primary">
                Tambah Koleksi
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" class="mb-3">
            <div class="input-group">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Cari judul atau kategori..."
                    value="{{ request('search') }}"
                >
                <button class="btn btn-dark">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Koleksi</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($koleksi as $k)
                    <tr>
                        <td>{{ $k->id_koleksi }}</td>
                        <td>{{ $k->buku->judul ?? '-' }}</td>
                        <td>{{ $k->buku->kategori ?? '-' }}</td>

                        <td>
                            @if($k->status_buku == 'Tersedia')
                                <span class="badge bg-success">Tersedia</span>
                            @elseif($k->status_buku == 'Dipinjam')
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            @elseif($k->status_buku == 'Rusak')
                                <span class="badge bg-danger">Rusak</span>
                            @else
                                <span class="badge bg-secondary">Hilang</span>
                            @endif
                        </td>

                        <td>{{ $k->lokasi_rak }}</td>
                        <td>{{ $k->kondisi_buku }}</td>

                        <td>

                            <a href="{{ route('koleksi.edit', $k->id_cp_koleksi) }}" class="btn btn-warning">
                                 Edit
                            </a>

                            <form action="/koleksi/delete/{{ $id }}" method="POST">
                            @csrf
                            @method('DELETE')
                                <button 
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus koleksi ini?')"
                                >
                                     Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $koleksi->links() }}
        </div>

    </div>

</div>

</body>
</html>
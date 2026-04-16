<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Koleksi</title>

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
        .form-control, .form-select {
            border-radius: 10px;
        }
        .btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">

    <div class="card shadow p-4" style="width: 500px;">

        <h3 class="text-center mb-4">Edit Koleksi Buku</h3>

        <!-- ALERT SUCCESS -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- ALERT ERROR -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM -->
        <form action="{{ route('koleksi.update', $koleksi->id_cp_koleksi) }}" method="POST">
        @csrf
        @method('PUT')

            <!-- ID KOLEKSI (READONLY) -->
            <div class="mb-3">
                <label class="form-label">ID Koleksi</label>
                <input type="text" class="form-control" value="{{ $koleksi->id_koleksi }}" readonly>
            </div>

            <!-- JUDUL BUKU -->
            <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <input type="text" class="form-control" value="{{ $koleksi->buku->judul ?? '-' }}" readonly>
            </div>

            <!-- STATUS -->
            <div class="mb-3">
                <label class="form-label">Status Buku</label>
                <select name="status_buku" class="form-select">
                    <option value="Tersedia" {{ $koleksi->status_buku == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ $koleksi->status_buku == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Rusak" {{ $koleksi->status_buku == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="Hilang" {{ $koleksi->status_buku == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>

            <!-- LOKASI -->
            <div class="mb-3">
                <label class="form-label">Lokasi Rak</label>
                <input 
                    type="text" 
                    name="lokasi_rak" 
                    class="form-control" 
                    value="{{ $koleksi->lokasi_rak }}" 
                    placeholder="Contoh: Rak A1"
                >
            </div>

            <!-- KONDISI -->
            <div class="mb-3">
                <label class="form-label">Kondisi Buku</label>
                <select name="kondisi_buku" class="form-select">
                    <option {{ $koleksi->kondisi_buku == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option {{ $koleksi->kondisi_buku == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option {{ $koleksi->kondisi_buku == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <!-- BUTTON -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    Update Koleksi
                </button>

                <a href="{{ route('koleksi.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>

    </div>

</div>

</body>
</html>
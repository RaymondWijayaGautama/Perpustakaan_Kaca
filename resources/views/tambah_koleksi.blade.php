<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Koleksi</title>

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
        
        <h3 class="text-center mb-4">Tambah Koleksi Buku</h3>

        <!-- SUCCESS -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- ERROR -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('koleksi.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Pilih Buku</label>
                <select name="ISBN" class="form-select" required>
                    <option value="">-- Pilih Buku --</option>
                    @foreach($buku as $b)
                        <option value="{{ $b->ISBN }}">
                            {{ $b->judul_koleksi }} ({{ $b->ISBN }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Buku</label>
                <input 
                    type="number" 
                    name="jumlah" 
                    class="form-control"
                    min="1" 
                    value="1"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Status Buku</label>
                <select name="status_buku" class="form-select">
                    <option value="Tersedia">Tersedia</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi Rak</label>
                <input 
                    type="text" 
                    name="lokasi_rak" 
                    class="form-control" 
                    placeholder="Contoh: Rak A1"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Masuk</label>
                <input 
                    type="date" 
                    name="tanggal_masuk" 
                    class="form-control"
                    value="{{ date('Y-m-d') }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Kondisi Buku</label>
                <select name="kondisi_buku" class="form-select" required>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    Simpan Koleksi
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
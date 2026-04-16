<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Buku</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #275f9c);
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    
    <div class="card shadow p-4" style="width: 500px;">
        
        <h3 class="text-center mb-4">📚 Tambah Buku</h3>

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

        <form action="{{ route('buku.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">ISBN</label>
                <input type="text" name="ISBN" class="form-control" placeholder="Contoh: 1-4028-9462-7">
            </div>

            <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="judul" class="form-control" placeholder="Masukkan Judul">
            </div>

            <div class="mb-3">
                <label class="form-label">Penulis</label>
                <input type="text" name="penulis" class="form-control" placeholder="Nama Penulis">
            </div>

            <div class="mb-3">
                <label class="form-label">Penerbit</label>
                <input type="text" name="penerbit" class="form-control" placeholder="Nama Penerbit">
            </div>

            <div class="mb-3">
                <label class="form-label">Tahun Terbit</label>

                <!-- Dropdown -->
                <select class="form-control mb-2" onchange="isiTahun(this.value)">
                    <option value="">-- Pilih Tahun --</option>

                    <!-- Generate dari tahun sekarang -->
                    @for ($i = date('Y'); $i >= 1990; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>

                <!-- Input manual -->
                <input type="number" name="tahun_terbit" id="tahunInput" class="form-control" placeholder="Atau ketik tahun (contoh: 2024)">
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select class="form-control mb-2" onchange="isiKategori(this.value)">
                    <option value="">-- Pilih Kategori --</option>

                    <option value="Fiksi">Fiksi</option>
                    <option value="Nonfiksi">Nonfiksi</option>
                    <option value="Novel">Novel</option>
                    <option value="Cerpen">Cerpen</option>
                    <option value="Puisi">Puisi</option>
                    <option value="Drama">Drama</option>
                    <option value="Romantis">Romantis</option>
                    <option value="Fantasi">Fantasi</option>
                    <option value="Fiksi Ilmiah (Sci-Fi)">Fiksi Ilmiah (Sci-Fi)</option>
                    <option value="Misteri">Misteri</option>
                    <option value="Detektif">Detektif</option>
                    <option value="Horor">Horor</option>
                    <option value="Thriller">Thriller</option>
                    <option value="Petualangan">Petualangan</option>
                    <option value="Sejarah (Fiksi Sejarah)">Sejarah (Fiksi Sejarah)</option>
                    <option value="Komedi">Komedi</option>
                    <option value="Action">Action</option>

                    <option value="Biografi">Biografi</option>
                    <option value="Autobiografi">Autobiografi</option>
                    <option value="Esai">Esai</option>
                    <option value="Jurnal">Jurnal</option>

                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Teknologi">Teknologi</option>
                    <option value="Komputer">Komputer</option>
                    <option value="Bisnis">Bisnis</option>
                    <option value="Ekonomi">Ekonomi</option>
                    <option value="Manajemen">Manajemen</option>
                    <option value="Pengembangan Diri">Pengembangan Diri</option>
                    <option value="Motivasi">Motivasi</option>
                    <option value="Psikologi">Psikologi</option>

                    <option value="Kesehatan">Kesehatan</option>
                    <option value="Kedokteran">Kedokteran</option>
                    <option value="Hukum">Hukum</option>
                    <option value="Politik">Politik</option>
                    <option value="Agama">Agama</option>
                    <option value="Sejarah">Sejarah</option>
                    <option value="Sains">Sains</option>
                    <option value="Matematika">Matematika</option>
                    <option value="Bahasa">Bahasa</option>

                    <option value="Seni">Seni</option>
                    <option value="Desain">Desain</option>
                    <option value="Musik">Musik</option>
                    <option value="Fotografi">Fotografi</option>
                    <option value="Kuliner">Kuliner</option>
                    <option value="Memasak">Memasak</option>
                    <option value="Pariwisata">Pariwisata</option>
                    <option value="Travel">Travel</option>
                    <option value="Olahraga">Olahraga</option>

                    <option value="Parenting">Parenting</option>
                    <option value="Pertanian">Pertanian</option>
                    <option value="Lingkungan">Lingkungan</option>

                    <option value="Anak-anak">Anak-anak</option>
                    <option value="Remaja">Remaja</option>
                    <option value="Dewasa">Dewasa</option>

                    <option value="Buku Cetak">Buku Cetak</option>
                    <option value="E-book">E-book</option>
                    <option value="Audiobook">Audiobook</option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                     Simpan Buku
                </button>
            </div>

        </form>
    </div>

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Buku - Wigaty Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Import Data Buku Koleksi</h4>
                    <small>Perpustakaan Wigaty Library</small>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('pustakawan.buku.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="nip_karyawan" value="{{ $nipKaryawan }}">

                        <div class="mb-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <label for="file_excel" class="form-label fw-bold mb-0">Pilih File Excel (.xlsx / .xls / .csv)</label>
                                <a href="{{ route('pustakawan.buku.import_template') }}" class="btn btn-outline-primary btn-sm">
                                    Download Template Impor
                                </a>
                            </div>
                            <input class="form-control form-control-lg" type="file" id="file_excel" name="file_excel" required accept=".xlsx, .xls, .csv">
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading fw-bold">Panduan Kolom Excel:</h6>
                            Gunakan template impor buku induk. Data buku diisi mulai baris ke-4 dengan susunan kolom:
                            <hr>
                            <p class="mb-2">
                                <code>No. Induk</code> |
                                <code>No. Kode</code> |
                                <code>Pengarang</code> |
                                <code>Judul</code> |
                                <code>Penerbit, Kota, Tahun, Cet.</code> |
                                <code>Tanggal diterima</code> |
                                <code>Jumlah Eksemplar</code> |
                                <code>Jumlah Halaman</code> |
                                <code>Ukuran Buku</code> |
                                <code>Bibliografi</code> |
                                <code>Indeks</code> |
                                <code>ISBN</code> |
                                <code>Keterangan</code>
                            </p>
                            <p class="mb-0 small">
                                Kolom <strong>No. Kode</strong> dibaca sebagai nomor kategori buku.
                                Tahun akan diambil dari kolom <strong>Penerbit, Kota, Tahun, Cet.</strong>.
                                <strong>Jumlah Eksemplar</strong> akan membuat jumlah copy fisik sesuai angka yang diisi.
                            </p>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ url('/') }}" class="btn btn-secondary px-4">Kembali</a>
                            <button type="submit" class="btn btn-primary px-4">Mulai Import</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

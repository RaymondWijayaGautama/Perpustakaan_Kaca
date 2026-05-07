<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Pengunjung</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fc;
        }
        .card-search {
            border-radius: 12px;
        }
        .result-item {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            transition: 0.2s;
        }
        .result-item:hover {
            background-color: #f1f3f9;
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <!-- HEADER -->
    <div class="mb-4">
        <h3 class="fw-bold">Pencarian Pengunjung</h3>
        <p class="text-muted">Cari berdasarkan Nama / NISN / ID</p>
    </div>

    <!-- SEARCH BOX -->
    <div class="card shadow card-search">
        <div class="card-body">

            <input 
                type="text" 
                id="search" 
                class="form-control form-control-lg" 
                placeholder="Ketik nama / NISN / ID..."
            >

        </div>
    </div>

    <!-- RESULT -->
    <div id="result" class="mt-4"></div>

</div>

<script>
let delay;

$('#search').on('keyup', function() {
    clearTimeout(delay);

    let keyword = $(this).val();

    delay = setTimeout(function() {

        if(keyword.length === 0){
            $('#result').html('');
            return;
        }

        $.ajax({
            url: '/pustakawan/pengunjung/search',
            type: 'GET',
            data: { q: keyword },
            success: function(data) {

                let html = '';

                if(data.length === 0){
                    html = `
                        <div class="alert alert-warning text-center">
                            Data tidak ditemukan
                        </div>
                    `;
                } else {

                    html += `<div class="card shadow"><div class="card-body">`;

                    data.forEach(function(item){
                        html += `
                            <div class="result-item">
                                <div class="row">
                                    <div class="col-md-8 text-start">
                                        <h5 class="mb-1 fw-bold">${item.nama_siswa_tetap}</h5>
                                        <small class="text-muted">
                                            NISN: ${item.nisn_siswa} | ID: ${item.id_siswa_tetap}
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-primary">Pengunjung</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    html += `</div></div>`;
                }

                $('#result').html(html);
            },
            error: function() {
                $('#result').html(`
                    <div class="alert alert-danger text-center">
                        Terjadi error
                    </div>
                `);
            }
        });

    }, 300);
});
</script>

</body>
</html>
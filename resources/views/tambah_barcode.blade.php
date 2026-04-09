_<!DOCTYPE html>
<html>
<head>
    <title>Sistem Perpustakaan - Input Koleksi Baru</title>
</head>
<body style="padding: 50px; font-family: Arial; text-align: center;">
    <h2>Form Input Buku Fisik Baru (Pustakawan)</h2>
    <p>Masukkan ISBN buku untuk meng-generate Barcode unik sistem.</p>
    
    <form action="/generate-barcode" method="POST" style="margin-top: 30px;">
        @csrf <input type="text" name="isbn" placeholder="Contoh: 696-696-696" required style="padding: 10px; width: 300px; font-size: 16px;">
        <br><br>
        <button type="submit" 
        style="padding: 10px 20px; background: #00f83e; color: rgb(255, 255, 255); border: none; cursor: pointer; font-size: 16px; border-radius: 5px;">Generate Barcode & Simpan</button>
    </form>
</body>
</html>
<?php
include 'koneksi.php';

if (isset($_POST['tambah_produk'])) {
    $nama_produk = mysqli_real_escape_string($konek, $_POST['nama_produk']);
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    $kategori    = mysqli_real_escape_string($konek, $_POST['kategori']);
    
    // Logika Upload Gambar
    $nama_file   = $_FILES['foto']['name'];
    $tmp_file    = $_FILES['foto']['tmp_name'];
    
    // Jika admin memilih file gambar
    if (!empty($nama_file)) {
        // Ambil ekstensi filenya (jpg/png)
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        // Bikin nama unik baru supaya tidak bentrok (misal: 17123456_produk.png)
        $foto_baru = time() . "_" . $nama_produk . "." . $ekstensi;
        $jalur_simpan = "img/" . $foto_baru;
        
        // Pindahkan file dari komputer admin ke folder img/ project kita
        if (move_uploaded_file($tmp_file, $jalur_simpan)) {
            $query = "INSERT INTO produk (nama_produk, harga, stok, kategori, foto) 
                      VALUES ('$nama_produk', '$harga', '$stok', '$kategori', '$foto_baru')";
        } else {
            echo "<script>alert('Gagal mengunggah gambar!');</script>";
            $query = "INSERT INTO produk (nama_produk, harga, stok, kategori, foto) 
                      VALUES ('$nama_produk', '$harga', '$stok', '$kategori', NULL)";
        }
    } else {
        // Jika admin tidak mengunggah gambar, set NULL atau kosong
        $query = "INSERT INTO produk (nama_produk, harga, stok, kategori, foto) 
                  VALUES ('$nama_produk', '$harga', '$stok', '$kategori', NULL)";
    }

    if (mysqli_query($konek, $query)) {
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location='admin_produk.php';</script>";
    } else {
        echo "Error: " . mysqli_error($konek);
    }
}
?>

<form method="POST" action="" enctype="multipart/form-data" class="space-y-4 max-w-lg bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
    <h2 class="text-lg font-bold text-gray-800">Tambah Produk Baru</h2>
    
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Produk</label>
        <input type="text" name="nama_produk" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs focus:outline-none">
    </div>
    
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">Harga (Rp)</label>
            <input type="number" name="harga" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">Stok</label>
            <input type="number" name="stok" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs focus:outline-none">
        </div>
    </div>
    
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">Kategori</label>
        <select name="kategori" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs focus:outline-none">
            <option value="Alat Tulis">Alat Tulis</option>
            <option value="Fotocopy">Fotocopy</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">Foto Produk (.png / .jpg)</label>
        <input type="file" name="foto" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs focus:outline-none file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
    </div>

    <button type="submit" name="tambah_produk" class="w-full bg-[#8ba869] hover:bg-[#799658] text-white py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer">
        Simpan Produk
    </button>
</form>
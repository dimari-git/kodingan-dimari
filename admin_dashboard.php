<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$edit_mode = false;
$id_edit = $nama_edit = $kat_edit = $harga_edit = $stok_edit = "";

if (isset($_POST['simpan_produk'])) {
    $nama   = mysqli_real_escape_string($konek, $_POST['nama_produk']);
    $kat    = mysqli_real_escape_string($konek, $_POST['kategori']);
    $harga  = (int)$_POST['harga'];
    $stok   = (int)$_POST['stok'];
    $id_p   = $_POST['id_produk'];

    // --- KODE AMBIL DATA FILE FOTO BARU ---
    $nama_foto = $_FILES['foto']['name'];
    $tmp_foto  = $_FILES['foto']['tmp_name'];
    $foto_final = null;

    // Jika admin mengunggah foto, proses filenya
    if (!empty($nama_foto)) {
        $ekstensi = pathinfo($nama_foto, PATHINFO_EXTENSION);
        // Membuat nama unik agar file tidak saling menimpa di folder img/
        $foto_final = time() . "_" . str_replace(' ', '_', $nama) . "." . $ekstensi;
        move_uploaded_file($tmp_foto, "img/" . $foto_final);
    }

    if (!empty($id_p)) {
        // --- LOGIKA EDIT ---
        if (!empty($nama_foto)) {
            // Jika saat edit admin mengganti foto baru
            mysqli_query($konek, "UPDATE produk SET nama_produk='$nama', kategori='$kat', harga='$harga', stok='$stok', foto='$foto_final' WHERE id_produk='$id_p'");
        } else {
            // Jika saat edit admin tidak mengganti foto (tetap pakai foto lama)
            mysqli_query($konek, "UPDATE produk SET nama_produk='$nama', kategori='$kat', harga='$harga', stok='$stok' WHERE id_produk='$id_p'");
        }
        echo "<script>alert('Produk berhasil diperbarui!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        // --- LOGIKA TAMBAH BARU ---
        // Kolom 'foto' dimasukkan ke dalam query insert
        mysqli_query($konek, "INSERT INTO produk (nama_produk, kategori, harga, stok, foto) VALUES ('$nama', '$kat', '$harga', '$stok', " . ($foto_final ? "'$foto_final'" : "NULL") . ")");
        echo "<script>alert('Produk baru berhasil ditambahkan!'); window.location.href='admin_dashboard.php';</script>";
    }
}

if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($konek, $_GET['edit']);
    $q_edit  = mysqli_query($konek, "SELECT * FROM produk WHERE id_produk='$id_edit'");
    if ($r = mysqli_fetch_assoc($q_edit)) {
        $edit_mode  = true;
        $nama_edit  = $r['nama_produk'];
        $kat_edit   = $r['kategori'];
        $harga_edit = $r['harga'];
        $stok_edit  = $r['stok'];
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($konek, $_GET['hapus']);
    mysqli_query($konek, "DELETE FROM produk WHERE id_produk='$id_hapus'");
    echo "<script>alert('Produk berhasil dihapus!'); window.location.href='admin_dashboard.php';</script>";
}

$tampil_produk = mysqli_query($konek, "SELECT * FROM produk ORDER BY id_produk DESC");
$tampil_pesanan = mysqli_query($konek, "SELECT * FROM transaksi ORDER BY id_transaksi DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Toko - Admin Dimari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#f7f9f6] min-h-screen text-gray-800 p-4 md:p-8">

    <div class="max-w-6xl mx-auto space-y-8">
        
        <div class="flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-lg font-extrabold text-[#556b3f]">Kelola Toko Dimari </h1>
                <p class="text-[11px] text-gray-400">Welcome!</p>
            </div>
            <a href="index.php" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold transition">Keluar</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm h-fit">
                <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                    <?php echo $edit_mode ? '✏️ Edit Data Produk' : 'Tambah Produk Baru'; ?>
                </h3>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-3">
                    <input type="hidden" name="id_produk" value="<?php echo $id_edit; ?>">
                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-1">Nama Barang</label>
                        <input type="text" name="nama_produk" required value="<?php echo $nama_edit; ?>" placeholder="Text.." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-1">Kategori</label>
                        <select name="kategori" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                            <option value="Alat Tulis" <?php if($kat_edit == 'Alat Tulis') echo 'selected'; ?>>Alat Tulis</option>
                            <option value="Buku" <?php if($kat_edit == 'Buku') echo 'selected'; ?>>Buku</option>
                            <option value="Fotocopy" <?php if($kat_edit == 'Fotocopy') echo 'selected'; ?>>Fotocopy</option>
                            <option value="Perlengkapan" <?php if($kat_edit == 'Perlengkapan') echo 'selected'; ?>>Perlengkapan</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 mb-1">Harga (Rp)</label>
                            <input type="number" name="harga" required value="<?php echo $harga_edit; ?>" placeholder="4000" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 mb-1">Jumlah Stok</label>
                            <input type="number" name="stok" required value="<?php echo $stok_edit; ?>" placeholder="50" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-1">Foto Produk (.png)</label>
                        <input type="file" name="foto" accept="image/png, image/jpeg" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs text-gray-500 focus:outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button type="submit" name="simpan_produk" class="flex-1 bg-[#8ba869] hover:bg-[#799658] text-white py-2 rounded-xl text-xs font-bold transition cursor-pointer">
                            <?php echo $edit_mode ? 'Simpan Perubahan' : 'Tambahkan'; ?>
                        </button>
                        <?php if($edit_mode): ?>
                            <a href="admin_dashboard.php" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-3 py-2 rounded-xl text-xs text-center font-medium transition">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Daftar Etalase Produk</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 font-bold">
                                <th class="py-2">Nama Produk</th>
                                <th class="py-2">Kategori</th>
                                <th class="py-2 text-right">Harga</th>
                                <th class="py-2 text-center">Stok</th>
                                <th class="py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            <?php while($row = mysqli_fetch_assoc($tampil_produk)): ?>
                            <tr>
                                <td class="py-2.5 font-semibold text-gray-900"><?php echo $row['nama_produk']; ?></td>
                                <td class="py-2.5"><span class="bg-gray-100 px-2 py-0.5 rounded-full text-[10px]"><?php echo $row['kategori']; ?></span></td>
                                <td class="py-2.5 text-right font-medium text-[#556b3f]">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td class="py-2.5 text-center font-bold"><?php echo $row['stok']; ?></td>
                                <td class="py-2.5 text-center space-x-2">
                                    <a href="admin_dashboard.php?edit=<?php echo $row['id_produk']; ?>" class="text-blue-500 hover:underline">Edit</a>
                                    <a href="admin_dashboard.php?hapus=<?php echo $row['id_produk']; ?>" onclick="return confirm('Hapus barang ini dari toko?')" class="text-red-500 hover:underline">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Daftar Pesanan Masuk</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 font-bold">
                            <th class="py-2">No. Transaksi</th>
                            <th class="py-2">Nama Pembeli</th>
                            <th class="py-2">Alamat Pengirim</th>
                            <th class="py-2">Kontak</th>
                            <th class="py-2 text-right">Total</th>
                            <th class="py-2 text-center">Waktu Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-gray-700">
                        <?php if(mysqli_num_rows($tampil_pesanan) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-6 text-gray-400">Belum ada pesanan yang masuk dari halaman user.</td>
                            </tr>
                        <?php endif; ?>
                        <?php while($trans = mysqli_fetch_assoc($tampil_pesanan)): ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3 text-center font-mono font-bold text-gray-400">#TRX-<?php echo $trans['id_transaksi']; ?></td>
                            <td class="py-3 font-bold text-gray-900"><?php echo htmlspecialchars($trans['nama_pembeli']); ?></td>
                            <td class="py-3 text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($trans['alamat']); ?></td>
                            <td class="py-3 font-medium text-blue-600 underline"><?php echo htmlspecialchars($trans['kontak']); ?></td>
                            <td class="py-3 text-right font-extrabold text-[#556b3f]">Rp <?php echo number_format($trans['grand_total'], 0, ',', '.'); ?></td>
                            <td class="py-3 text-center text-gray-400 text-[11px]"><?php echo date('d/m/Y H:i', strtotime($trans['tanggal_pesan'])); ?> WIB</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
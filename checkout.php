<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$username_session = $_SESSION['user'];

$query_user = mysqli_query($konek, "SELECT * FROM users WHERE username = '$username_session'");
$data_user  = mysqli_fetch_assoc($query_user);

$nama_pembeli = $data_user['username']; 

$alamat_pembeli = isset($_POST['alamat_pembeli']) ? mysqli_real_escape_string($konek, $_POST['alamat_pembeli']) : '-';
$kontak_pembeli = isset($_POST['kontak_pembeli']) ? mysqli_real_escape_string($konek, $_POST['kontak_pembeli']) : '-';

$produk_belanjaan = [];
$grand_total = 0;
$total_barang = 0;

if (isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $id_produk => $quantity) {
        $quantity = (int)$quantity;
        
        if ($quantity > 0) {
            $id_produk = mysqli_real_escape_string($konek, $id_produk);
            $query = mysqli_query($konek, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
            
            if ($row = mysqli_fetch_assoc($query)) {
                $subtotal = $row['harga'] * $quantity;
                $grand_total += $subtotal;
                $total_barang += $quantity;
                
                $produk_belanjaan[] = [
                    'nama' => $row['nama_produk'],
                    'harga' => $row['harga'],
                    'qty' => $quantity,
                    'subtotal' => $subtotal
                ];
            }
        }
    }
}

if (empty($produk_belanjaan) && !isset($_POST['proses_simpan'])) {
    echo "<script>alert('Pilih minimal 1 produk sebelum checkout!'); window.location.href='toko.php';</script>";
    exit;
}

if (isset($_POST['proses_simpan'])) {
    $nama_fix   = mysqli_real_escape_string($konek, $_POST['nama_pembeli']);
    $alamat_fix = mysqli_real_escape_string($konek, $_POST['alamat_pembeli']);
    $kontak_fix = mysqli_real_escape_string($konek, $_POST['kontak_pembeli']);
    $total_fix  = (int)$_POST['grand_total'];

    $insert = mysqli_query($konek, "INSERT INTO transaksi (nama_pembeli, alamat, kontak, grand_total) 
                                    VALUES ('$nama_fix', '$alamat_fix', '$kontak_fix', '$total_fix')");
    if ($insert) {
        echo "sukses";
    } else {
        echo "gagal";
    }
    exit; //AJAX
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Pemesanan - Toko Dimari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#f7f9f6] min-h-screen pb-24 text-gray-800 flex items-center justify-center p-4 relative overflow-x-hidden">

    <div id="notif-sukses" class="fixed top-5 left-1/2 -translate-x-1/2 bg-[#556b3f] text-white px-6 py-3 rounded-full font-bold text-xs shadow-lg transition-all duration-300 opacity-0 transform -translate-y-10 z-50 pointer-events-none flex items-center gap-2">
        🎉 Pesanan sudah dikirim ke sistem!
    </div>

    <div class="w-full max-w-2xl bg-white rounded-3xl border border-gray-100 shadow-md p-6 md:p-8">
        
        <div class="text-center border-b border-dashed border-gray-200 pb-6 mb-6">
            <h2 class="text-2xl font-extrabold text-[#556b3f] tracking-wide">Toko Dimari</h2>
            <p class="text-xs text-gray-500 mt-1">Toko Alat Tulis & Jasa Fotocopy Berkualitas</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Tanggal Nota: <?php echo date('d M Y - H:i'); ?> WIB</p>
        </div>

        <div class="mb-6 bg-[#f8faf7] p-4 rounded-2xl border border-[#eef3ec] text-xs space-y-2">
            <div class="flex flex-col sm:flex-row sm:justify-between border-b border-gray-100 pb-1">
                <span class="text-gray-400">Nama:</span> 
                <span class="font-bold text-gray-700"><?php echo htmlspecialchars($nama_pembeli); ?></span>
            </div>
            <div class="flex flex-col sm:flex-row sm:justify-between border-b border-gray-100 pb-1">
                <span class="text-gray-400">Alamat:</span> 
                <span class="font-medium text-gray-700 sm:text-right max-w-xs"><?php echo htmlspecialchars($alamat_pembeli); ?></span>
            </div>
            <div class="flex flex-col sm:flex-row sm:justify-between">
                <span class="text-gray-400">Kontak:</span> 
                <span class="font-bold text-gray-700"><?php echo htmlspecialchars($kontak_pembeli); ?></span>
            </div>
        </div>

        <h3 class="text-sm font-bold text-gray-900 mb-3 tracking-tight">Rincian Item Belanja</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-400 font-semibold">
                        <th class="py-2.5">Nama Produk</th>
                        <th class="py-2.5 text-center">Jumlah</th>
                        <th class="py-2.5 text-right">Harga Satuan</th>
                        <th class="py-2.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    <?php foreach ($produk_belanjaan as $item): ?>
                    <tr>
                        <td class="py-3 font-medium text-gray-900 max-w-[200px] truncate"><?php echo $item['nama']; ?></td>
                        <td class="py-3 text-center font-bold text-gray-800"><?php echo $item['qty']; ?></td>
                        <td class="py-3 text-right">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td class="py-3 text-right font-semibold text-[#556b3f]">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="border-t border-dashed border-gray-200 mt-6 pt-4 space-y-2 text-xs">
            <div class="flex justify-between text-gray-500">
                <span>Total Kuantitas Barang:</span>
                <span class="font-bold text-gray-800"><?php echo $total_barang; ?> Item</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                <span class="text-sm font-bold text-gray-900">Grand Total Pembayaran:</span>
                <span class="text-xl font-extrabold text-[#556b3f]">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="text-center text-[11px] text-gray-400 mt-8 pt-4 border-t border-gray-100">
            <p>Terima kasih telah berbelanja di Matcha Stationery!</p>
        </div>

        <div class="mt-8">
            <button id="btn-checkout" onclick="prosesCheckout()" 
                    class="w-full bg-[#8ba869] hover:bg-[#799658] text-white py-3 rounded-xl font-bold text-xs text-center transition duration-200 shadow-md shadow-emerald-100 cursor-pointer">
                Checkout Sekarang
            </button>
        </div>
        
        <div class="text-center mt-4">
            <a href="toko.php" class="text-xs text-gray-400 hover:text-[#556b3f] transition underline">← Kembali Pilih Barang Lagi</a>
        </div>

    </div>

    <button id="btn-batal" onclick="batalkanPesanan()" 
            class="fixed bottom-5 right-5 bg-red-50 border border-red-200 text-red-600 px-4 py-2.5 rounded-full font-bold text-xs shadow-md hover:bg-red-100 transition-all duration-300 opacity-0 scale-75 pointer-events-none z-50 cursor-pointer">
        💔 Batalkan Pesanan?
    </button>

    <script>
    function prosesCheckout() {
        let formData = new FormData();
        formData.append('proses_simpan', '1');
        formData.append('nama_pembeli', '<?php echo $nama_pembeli; ?>');
        formData.append('alamat_pembeli', '<?php echo $alamat_pembeli; ?>');
        formData.append('kontak_pembeli', '<?php echo $kontak_pembeli; ?>');
        formData.append('grand_total', '<?php echo $grand_total; ?>'); // Mengirim nominal asli hasil loop hitungan PHP

        fetch('checkout.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            // Tampilkan Notifikasi Atas & Tombol Batal
            document.getElementById('notif-sukses').classList.remove('opacity-0', '-translate-y-10');
            document.getElementById('notif-sukses').classList.add('opacity-100', 'translate-y-0');
            
            let btnBatal = document.getElementById('btn-batal');
            btnBatal.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
            btnBatal.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');

            let btnCheckout = document.getElementById('btn-checkout');
            btnCheckout.disabled = true;
            btnCheckout.innerText = '✓ Berhasil Dicheckout';
            btnCheckout.classList.remove('bg-[#8ba869]', 'hover:bg-[#799658]');
            btnCheckout.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        });
    }

    function batalkanPesanan() {
        let konfirmasi = confirm("Apakah Anda yakin ingin membatalkan pesanan ini?");
        if (konfirmasi) {
            alert("Pesanan berhasil dibatalkan. Mengembalikan Anda ke halaman utama toko.");
            window.location.href = 'toko.php';
        }
    }
    </script>

</body>
</html>
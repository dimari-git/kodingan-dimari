<?php
include 'koneksi.php'; 
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$query = mysqli_query($konek, "SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Toko Dimari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#E1E5D3] min-h-screen pb-28 text-gray-800">

    <!-- NAVBAR -->
    <nav class="bg-white  border-b border-gray-100 py-4 px-6 sticky top-0 z-50 flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-xl font-bold text-[#556b3f] tracking-wide">Toko Dimari</span>
        </div>
        
        <div class="w-full md:w-96 relative">
            <input type="text" placeholder="Cari..." 
                   class="w-full bg-gray-100 pl-4 pr-10 py-2 rounded-full text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
        </div>

        <div class="flex items-center gap-4 text-xs font-medium">
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-200">
                <div class="w-6 h-6 rounded-full bg-[#8ba869] text-white flex items-center justify-center text-[10px] uppercase font-bold">
                    <?php echo substr($_SESSION['user'], 0, 1); ?>
                </div>
                <span class="text-gray-700"><?php echo $_SESSION['user']; ?></span>
            </div>
            <a href="index.php" class="text-red-500 hover:underline">Keluar</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-4 md:p-6">
        
        <div class="flex flex-wrap gap-2 mb-6 text-xs">
            <button type="button" class="bg-[#8ba869] text-white px-4 py-2 rounded-full font-medium">Semua</button>
            <button type="button" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full font-medium hover:bg-gray-50">Buku</button>
            <button type="button" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full font-medium hover:bg-gray-50">Alat Tulis</button>
            <button type="button" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full font-medium hover:bg-gray-50">Fotocopy</button>
            <button type="button" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full font-medium hover:bg-gray-50">Perlengkapan</button>
        </div>

        <div class="bg-[#ffffff] p-6 md:p-8 rounded-2xl mb-8 border border-[#e1eade]">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#2f4f3a] tracking-tight">Persiapan sekolah, <span class="text-[#556b3f]">tanpa ribet.</span></h2>
            <p class="text-1xs text-gray-600 mt-1 font-bold">Diskon Pelajar setiap hari Jum'at!!</p>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-5 tracking-tight">Katalog Produk</h3>

        <form action="checkout.php" method="POST">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between hover:shadow-md transition duration-200 relative group">
                    
                    <div>
                        <div class="w-full h-36 bg-gray-100 rounded-xl mb-3 overflow-hidden relative flex items-center justify-center text-xs text-gray-400 font-medium">
    
                            <?php if (!empty($row['foto']) && file_exists("img/" . $row['foto'])): ?>
                                <img src="img/<?php echo $row['foto']; ?>" alt="<?php echo $row['nama_produk']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-gray-400">Foto Produk</span>
                            <?php endif; ?>

                            <span class="absolute top-2 right-2 text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">
                                <?php echo $row['kategori']; ?>
                            </span>
                        </div>

                        <h4 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2"><?php echo $row['nama_produk']; ?></h4>
                        <p class="text-[11px] text-gray-400 mt-1">Stok: <?php echo $row['stok']; ?></p>
                    </div>

                    <!-- Bagian Harga & Tombol Sejajar Sesuai Gambar -->
                    <!-- Bagian Harga & Kuantitas Sejajar Rapi -->
            <div class="mt-4 flex items-center justify-between gap-2">
                <!-- Harga di Kiri -->
                <p class="text-base font-extrabold text-[#556b3f] whitespace-nowrap">
                    Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                </p>
                
                <!-- Tombol Kuantitas (Minus - Angka - Plus) di Kanan -->
                <div class="flex items-center bg-gray-100 rounded-full p-1 border border-gray-200 shadow-sm">
                    <!-- Tombol Minus -->
                    <button type="button" 
                            onclick="kurangQty(this)" 
                            class="w-7 h-7 flex items-center justify-center bg-white text-gray-600 rounded-full hover:bg-gray-200 transition duration-150 font-bold text-sm focus:outline-none select-none">
                        -
                    </button>
                    
                    <!-- Input Angka Kuantitas (Hidden Aslinya, Ditampilkan Teks) -->
                    <div class="px-2.5 text-center min-w-[24px]">
                        <span class="text-xs font-bold text-gray-800 qty-display">0</span>
                        <input type="number" name="qty[<?php echo $row['id_produk']; ?>]" value="0" min="0" max="<?php echo $row['stok']; ?>"
                            class="hidden input-qty" 
                            data-harga="<?php echo $row['harga']; ?>" 
                            onchange="hitungOtomatisTotal()">
                    </div>
                    
                    <!-- Tombol Plus -->
                    <button type="button" 
                            onclick="tambahQty(this)" 
                            class="w-7 h-7 flex items-center justify-center bg-[#8ba869] text-white rounded-full hover:bg-[#799658] transition duration-150 font-bold text-sm focus:outline-none select-none shadow-sm">
                        +
                    </button>
                </div>
            </div>

                </div>
                <?php endwhile; ?>
            </div>

            <!-- STICKY BAR BAWAH -->
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] z-40">
                <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full md:w-auto flex-1">
                        <input type="text" name="nama_pembeli" placeholder="Nama Pembeli..." required
                            class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                        <input type="text" name="alamat_pembeli" placeholder="Alamat Lengkap..." required
                            class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                        <input type="tel" name="kontak_pembeli" placeholder="No. HP / WhatsApp..." required
                            class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#8ba869]">
                    </div>

                    <div class="flex justify-between md:justify-end items-center gap-6 w-full md:w-auto shrink-0">
                        <div class="flex gap-4">
                            <div class="text-gray-500 text-xs">
                                Total Item: <span id="total-item" class="font-bold text-gray-800 text-sm">0</span>
                            </div>
                            <div class="text-gray-500 text-xs">
                                Subtotal: <span class="text-base font-extrabold text-[#556b3f]">Rp <span id="total-harga">0</span></span>
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-[#8ba869] hover:bg-[#799658] text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-sm transition duration-200 cursor-pointer">
                            Checkout Sekarang
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>

    <!-- JAVASCRIPT UNTUK HITUNG OTOMATIS -->
    <script>
// Fungsi ketika tombol (+) diklik
function tambahQty(button) {
    let wrapper = button.parentElement;
    let input = wrapper.querySelector('.input-qty');
    let display = wrapper.querySelector('.qty-display');
    let maxStok = parseInt(input.getAttribute('max'));
    
    let currentVal = parseInt(input.value);
    if (currentVal < maxStok) {
        input.value = currentVal + 1;
        display.innerText = input.value;
        hitungOtomatisTotal();
    } else {
        alert('Stok produk tidak mencukupi!');
    }
}

// Fungsi ketika tombol (-) diklik
function kurangQty(button) {
    let wrapper = button.parentElement;
    let input = wrapper.querySelector('.input-qty');
    let display = wrapper.querySelector('.qty-display');
    
    let currentVal = parseInt(input.value);
    if (currentVal > 0) {
        input.value = currentVal - 1;
        display.innerText = input.value;
        hitungOtomatisTotal();
    }
}

// Fungsi menghitung total item dan subtotal harga secara real-time
function hitungOtomatisTotal() {
    let semuaInputQty = document.querySelectorAll('.input-qty');
    let totalHarga = 0;
    let totalItem = 0;

    semuaInputQty.forEach(input => {
        let qty = parseInt(input.value);
        let harga = parseInt(input.getAttribute('data-harga'));
        
        if (qty > 0) {
            totalItem += qty;
            totalHarga += (qty * harga);
        }
    });

    // Update tampilan angka di sticky bar bawah
    document.getElementById('total-item').innerText = totalItem;
    document.getElementById('total-harga').innerText = totalHarga.toLocaleString('id-ID');
}
</script>

</body>
</html>
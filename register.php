<?php
include 'koneksi.php';
session_start();

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($konek, $_POST['username']);
    $email    = mysqli_real_escape_string($konek, $_POST['email']);
    $password = mysqli_real_escape_string($konek, $_POST['password']);
    $level    = 'user'; 

    $cek_user = mysqli_query($konek, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username atau Email sudah digunakan!";
    } else {
        $query_reg = mysqli_query($konek, "INSERT INTO users (username, email, password, level) 
                                           VALUES ('$username', '$email', '$password', '$level')");
        
        if ($query_reg) {
            $success = "Pendaftaran berhasil! Silakan login.";
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($konek);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Matcha Stationery</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#f7f9f6] min-h-screen text-gray-800 flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-3xl border border-gray-100 shadow-md overflow-hidden grid md:grid-cols-2">
        
        <div class="bg-[#556b3f] p-8 md:p-12 flex flex-col justify-between text-white relative">
            <div>
                <h2 class="text-2xl font-extrabold tracking-wide">Toko Dimari</h2>
                <p class="text-xs text-emerald-100/80 mt-1">Toko Alat Tulis & Jasa Fotocopy Berkualitas</p>
            </div>
            <div class="my-8 md:my-0">
                <h3 class="text-3xl font-extrabold leading-tight">Ayo Buat Akunmu</h3>
                <p class="text-xs text-emerald-100/70 mt-2">dan nikmati kemudahan bertransaksi cepat.</p>
            </div>
            <div class="text-[11px] text-emerald-200/50">
                ✨
            </div>
        </div>

        <div class="p-8 md:p-12 flex flex-col justify-center">
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Buat Akun Baru</h2>
            <p class="text-xs text-gray-400 mt-1 mb-6">Lengkapi data di bawah untuk mendaftar.</p>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 text-xs p-3 rounded-xl mb-4 border border-red-100 text-center font-medium"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-700 text-xs p-3 rounded-xl mb-4 border border-emerald-100 text-center font-medium">
                    <?php echo $success; ?> <a href="index.php" class="underline font-bold">Login di sini</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Username</label>
                    <input type="text" name="username" required placeholder="Username" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-[#8ba869] focus:outline-none text-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Alamat Email</label>
                    <input type="email" name="email" required placeholder="nama@sekolah.id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-[#8ba869] focus:outline-none text-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-[#8ba869] focus:outline-none text-gray-700">
                </div>

                <button type="submit" name="register" class="w-full bg-[#8ba869] hover:bg-[#799658] text-white py-2.5 rounded-xl text-xs font-bold transition duration-200 cursor-pointer shadow-md shadow-emerald-50 mt-2">
                    Daftar Sekarang
                </button>
            </form>

            <div class="text-center mt-6 text-xs text-gray-400">
                Sudah punya akun? <a href="index.php" class="text-[#556b3f] font-bold hover:underline">Masuk kembali</a>
            </div>
        </div>

    </div>

</body>
</html>
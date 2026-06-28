<?php
include 'koneksi.php';
session_start();
$error = "";

if (isset($_POST['login'])) {
    // Ambil data input dan amankan dari SQL Injection
    $username = mysqli_real_escape_string($konek, $_POST['username']);
    $password = mysqli_real_escape_string($konek, $_POST['password']);

    // Cek ke database berdasarkan username/email DAN password
    $query = mysqli_query($konek, "SELECT * FROM users WHERE (username='$username' OR email='$username') AND password='$password'");

    if (mysqli_num_rows($query) === 1) {
        $data = mysqli_fetch_assoc($query);

        // Cek Level Role masing-masing akun
        if ($data['level'] === 'admin') {
            $_SESSION['admin'] = $data['username']; // Set session khusus admin
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $_SESSION['user'] = $data['username']; // Set session khusus user biasa
            header("Location: toko.php");
            exit;
        }
    } else {
        $error = "Email/Username atau Password salah!";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang! - Toko Dimari</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 h-screen flex">

    <div class="hidden md:flex md:w-1/2 bg-cover bg-center relative p-12 flex-col justify-between text-white" 
         style="background-image: linear-gradient(to bottom, rgba(82, 117, 74, 0.85), rgba(47, 79, 48, 0.9)), url('bg.png');">
        
        <div class="flex items-center gap-2">
            <span class="text-xl font-bold tracking-wide">Toko Dimari</span>
        </div>

        <div class="max-w-md">
            <h1 class="text-4xl font-bold leading-tight mb-4">Belanja alat tulis mu</h1>
            <p class="text-sm text-gray-200 leading-relaxed">Diskon pelajar setiap hari</p>
        </div>

        <div class="text-xs text-emerald-200 flex items-center gap-2">
            <span>✨</span>
        </div>
    </div>

    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8 md:p-16">
        <div class="w-full max-w-md">
            
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">Selamat datang kembali</h2>
            <p class="text-sm text-gray-500 mb-8">Masuk untuk mulai belanja.</p>

        

            <form method="POST" action="" class="space-y-4">
    
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Email atau Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username/email" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-[#8ba869] focus:outline-none text-gray-700">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-[#8ba869] focus:outline-none text-gray-700">
                </div>

                <button type="submit" name="login" class="w-full bg-[#8ba869] hover:bg-[#799658] text-white py-2.5 rounded-xl text-xs font-bold transition duration-200 cursor-pointer shadow-md shadow-emerald-50 mt-4">
                    Masuk
                </button>
            </form>

<div class="text-center mt-6 text-xs text-gray-400">
    Belum punya akun? <a href="register.php" class="text-[#556b3f] font-bold hover:underline">Daftar</a>
</div>


</body>
</html>
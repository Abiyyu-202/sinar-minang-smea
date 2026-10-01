<?php
session_start();
$is_logged_in = isset($_SESSION['nama']) && $_SESSION['role'] == 'user';
$admin_logged_in = isset($_SESSION['nama']) && $_SESSION['role'] == 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinar Minang SMEA - Cita Rasa Padang Asli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }</style>
</head>
<body class="bg-slate-50 text-slate-800 selection:bg-red-200 selection:text-red-900">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md fixed top-0 w-full z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-red-600 flex items-center gap-2">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm4.75 2c0 3-2.5 3.6-2.5 3.6h1.5V22h2.5V2h-2.5c0 0 1 2.5 1 9z"/></svg>
                        Sinar Minang
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-red-600 font-semibold transition">Beranda</a>
                    <a href="menu.php" class="text-slate-600 hover:text-red-600 font-medium transition">Menu Spesial</a>
                    
                    <?php if($is_logged_in): ?>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-slate-500">Halo, <strong class="text-slate-800"><?= htmlspecialchars($_SESSION['nama']) ?></strong></span>
                            <a href="logout.php" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-full transition">Keluar</a>
                        </div>
                    <?php elseif($admin_logged_in): ?>
                        <a href="dashboard.php" class="px-5 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-900 rounded-full transition">Dashboard Admin</a>
                    <?php else: ?>
                        <div class="flex gap-3">
                            <a href="login.php" class="px-5 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-full transition">Masuk</a>
                            <a href="register.php" class="px-5 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200 rounded-full transition">Daftar</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Parallax -->
    <div class="relative w-full h-screen bg-fixed bg-cover bg-center flex items-center justify-center" style="background-image: url('Gambar/restoran_padang.jpeg');">
        <!-- Dark Overlay with Blur -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
            <span class="inline-block py-1.5 px-4 rounded-full bg-white/10 text-red-100 text-sm font-semibold mb-6 tracking-wide border border-white/20 backdrop-blur-md shadow-sm">
                ✨ Masakan Padang #1 di Magelang
            </span>
            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-7xl leading-tight mb-6">
                <span class="block drop-shadow-lg">Nikmati Kelezatan</span>
                <span class="block text-red-500 drop-shadow-lg">Rendang Juara</span>
            </h1>
            <p class="text-base text-slate-200 sm:text-lg sm:max-w-2xl sm:mx-auto md:text-xl mb-10 drop-shadow-md">
                Rasakan sensasi bumbu rempah asli Minangkabau dalam setiap gigitan. Dimasak lambat untuk kesempurnaan rasa yang tak terlupakan.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="menu.php" class="w-full sm:w-auto flex items-center justify-center px-10 py-4 border border-transparent text-base font-bold rounded-full text-white bg-red-600 hover:bg-red-700 shadow-xl shadow-red-900/40 transition md:text-lg transform hover:-translate-y-1">
                    Pesan Sekarang
                </a>
                <?php if(!$is_logged_in && !$admin_logged_in): ?>
                <a href="register.php" class="w-full sm:w-auto flex items-center justify-center px-10 py-4 border-2 border-white/30 text-base font-bold rounded-full text-white bg-white/10 hover:bg-white/20 backdrop-blur-md transition md:text-lg transform hover:-translate-y-1">
                    Buat Akun
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Feature Section -->
    <div class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base text-red-600 font-semibold tracking-wide uppercase">Mengapa Kami?</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Kualitas Tanpa Kompromi
                </p>
            </div>

            <div class="mt-20">
                <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-red-100 text-red-600 mx-auto mb-6">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Pelayanan Cepat</h3>
                        <p class="text-slate-500">Pesanan Anda siap dalam hitungan menit. Panas, segar, dan langsung bisa dinikmati.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-red-100 text-red-600 mx-auto mb-6">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Bumbu Otentik</h3>
                        <p class="text-slate-500">Diramu langsung dari rempah-rempah pilihan warisan turun-temurun asli Pariaman.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-16 w-16 rounded-2xl bg-red-100 text-red-600 mx-auto mb-6">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Kebersihan Terjamin</h3>
                        <p class="text-slate-500">Dapur terbuka kami dikelola dengan standar kebersihan tertinggi untuk kenyamanan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lokasi Maps -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900">Lokasi Kami</h2>
                <p class="mt-4 text-lg text-slate-500">Kunjungi restoran kami secara langsung di Magelang</p>
            </div>
            <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200">
               <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5068.797981458681!2d110.22154831155632!3d-7.450312092529853!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a85e87d740c9b%3A0x688883a50dc2f187!2sSMK%20Negeri%202%20Magelang!5e1!3m2!1sid!2sid!4v1790860370367!5m2!1sid!2sid" class="w-full" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe> 
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
            <div>
                <h4 class="text-white text-lg font-bold mb-4">Sinar Minang SMEA</h4>
                <p class="text-sm leading-relaxed">Cita rasa asli Padang di jantung kota Magelang. Melayani dengan hati, memasak dengan tradisi.</p>
            </div>
            <div>
                <h4 class="text-white text-lg font-bold mb-4">Kontak Kami</h4>
                <ul class="text-sm space-y-2">
                    <li>Jl. Ahmad Yani No.135A, Magelang</li>
                    <li>Telp: (021) 123-4567</li>
                    <li>Email: halo@sinarminang.com</li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-lg font-bold mb-4">Jam Buka</h4>
                <ul class="text-sm space-y-2">
                    <li>Senin - Jumat: 08.00 - 21.00</li>
                    <li>Sabtu - Minggu: 07.00 - 22.00</li>
                </ul>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-slate-800 text-center text-sm">
            <p>&copy; 2026 Sinar Minang SMEA. Hak cipta dilindungi.</p>
        </div>
    </footer>
</body>
</html>
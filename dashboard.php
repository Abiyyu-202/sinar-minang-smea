<?php
session_start();
if (!isset($_SESSION["nama"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}
include("connection.php");

// Fetch counts
$menu_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM menu"))['c'];
$drink_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM minuman"))['c'];
$order_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM orders WHERE status != 'Selesai'"))['c'];
$revenue = mysqli_fetch_assoc(mysqli_query($link, "SELECT SUM(total_harga) as s FROM orders WHERE status = 'Selesai'"))['s'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sinar Minang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white min-h-screen fixed left-0 top-0 hidden md:block z-50">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-red-500 mb-8">Admin Panel</h2>
            <ul class="space-y-3">
                <li><a href="dashboard.php" class="block px-4 py-3 rounded-lg bg-red-600 text-white font-medium shadow-lg shadow-red-900/20">Dashboard</a></li>
                <li><a href="admin_orders.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Pesanan Masuk</a></li>
                <li><a href="admin_menu.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Kelola Menu</a></li>
            </ul>
        </div>
        <div class="absolute bottom-0 w-full p-6 border-t border-slate-800">
            <a href="index.php" target="_blank" class="block px-4 py-2 mb-2 text-center text-sm text-slate-400 hover:text-white">Lihat Website</a>
            <a href="logout.php" class="block px-4 py-2 text-center bg-slate-800 hover:bg-slate-700 rounded-lg text-sm text-red-400 transition">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 md:ml-64 p-8">
        <header class="mb-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Dashboard Overview</h1>
                <p class="text-slate-500 mt-1">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']) ?>!</p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <!-- Stat Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm border-b-4 border-b-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Pesanan Aktif</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $order_count ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 text-xl">🛒</div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm border-b-4 border-b-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Pendapatan</p>
                        <h3 class="text-3xl font-bold text-slate-800">Rp <?= number_format($revenue ?? 0, 0, ',', '.') ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-xl">💰</div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm border-b-4 border-b-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">Total Menu Aktif</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $menu_count + $drink_count ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-xl">🍲</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Ringkasan Aktivitas</h3>
            </div>
            <div class="p-6">
                <p class="text-slate-500">Anda dapat memantau semua pesanan dan mengatur hidangan restoran melalui menu di sebelah kiri.</p>
                <div class="mt-6 flex gap-4">
                    <a href="admin_orders.php" class="px-6 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Cek Pesanan Masuk</a>
                    <a href="admin_menu.php" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition">Tambah Menu Baru</a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>

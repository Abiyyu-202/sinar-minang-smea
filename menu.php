<?php
session_start();
include("connection.php");

// Fetch menu items
$menu_items = mysqli_query($link, "SELECT * FROM menu ORDER BY id DESC");
$drinks = mysqli_query($link, "SELECT * FROM minuman ORDER BY id DESC");

$is_logged_in = isset($_SESSION['nama']) && $_SESSION['role'] == 'user';
$admin_logged_in = isset($_SESSION['nama']) && $_SESSION['role'] == 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Sinar Minang SMEA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        /* Hide arrows from number input */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-red-600 flex items-center gap-2">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm4.75 2c0 3-2.5 3.6-2.5 3.6h1.5V22h2.5V2h-2.5c0 0 1 2.5 1 9z"/></svg>
                        Sinar Minang
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-slate-600 hover:text-red-600 font-medium transition">Beranda</a>
                    <a href="menu.php" class="text-red-600 font-semibold transition">Menu Spesial</a>
                    
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h1 class="text-4xl font-bold text-slate-900 mb-4">Eksplorasi Rasa Autentik</h1>
            <p class="text-lg text-slate-500">Pilih hidangan favoritmu, pesan dengan mudah, dan nikmati cita rasa asli Minangkabau.</p>
        </div>

        <form action="order.php" method="POST" id="orderForm">
            
            <!-- Food Section -->
            <div class="mb-16">
                <div class="flex items-center gap-4 mb-8">
                    <h2 class="text-2xl font-bold text-slate-800">Makanan Utama</h2>
                    <div class="h-px bg-slate-200 flex-1"></div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php while ($item = mysqli_fetch_assoc($menu_items)) : 
                        $item_id = strtolower(str_replace(' ', '_', $item['menu']));
                        $img_src = strpos($item['gambar'], 'http') === 0 ? htmlspecialchars($item['gambar']) : 'picture/' . htmlspecialchars($item['gambar']);
                    ?>
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col">
                        <div class="relative h-48 overflow-hidden">
                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($item['menu']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-red-600 shadow-sm">
                                Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-bold text-lg text-slate-800 mb-2"><?= htmlspecialchars($item['menu']); ?></h3>
                            <p class="text-sm text-slate-500 line-clamp-2 mb-4 flex-1"><?= htmlspecialchars($item['deskripsi']); ?></p>
                            
                            <?php if($is_logged_in): ?>
                            <div class="flex items-center justify-between bg-slate-50 p-2 rounded-xl border border-slate-100">
                                <button type="button" onclick="updateQty('<?= $item_id ?>', -1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:text-red-600 hover:bg-red-50 shadow-sm transition">-</button>
                                <input type="number" id="<?= $item_id ?>-qty" name="<?= $item_id ?>" value="0" min="0" class="w-12 text-center bg-transparent font-semibold text-slate-800" readonly>
                                <button type="button" onclick="updateQty('<?= $item_id ?>', 1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:text-red-600 hover:bg-red-50 shadow-sm transition">+</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Drinks Section -->
            <div class="mb-24">
                <div class="flex items-center gap-4 mb-8">
                    <h2 class="text-2xl font-bold text-slate-800">Minuman Segar</h2>
                    <div class="h-px bg-slate-200 flex-1"></div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php while ($drink = mysqli_fetch_assoc($drinks)) : 
                        $drink_id = strtolower(str_replace(' ', '_', $drink['minuman']));
                        $img_src = strpos($drink['gambar'], 'http') === 0 ? htmlspecialchars($drink['gambar']) : 'picture/' . htmlspecialchars($drink['gambar']);
                    ?>
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col">
                        <div class="relative h-48 overflow-hidden bg-slate-100">
                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($drink['minuman']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-red-600 shadow-sm">
                                Rp <?= number_format($drink['harga'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-bold text-lg text-slate-800 mb-2"><?= htmlspecialchars($drink['minuman']); ?></h3>
                            <p class="text-sm text-slate-500 line-clamp-2 mb-4 flex-1"><?= htmlspecialchars($drink['deskripsi'] ?? 'Penyegar dahaga yang nikmat.'); ?></p>
                            
                            <?php if($is_logged_in): ?>
                            <div class="flex items-center justify-between bg-slate-50 p-2 rounded-xl border border-slate-100">
                                <button type="button" onclick="updateQty('<?= $drink_id ?>', -1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:text-red-600 hover:bg-red-50 shadow-sm transition">-</button>
                                <input type="number" id="<?= $drink_id ?>-qty" name="<?= $drink_id ?>" value="0" min="0" class="w-12 text-center bg-transparent font-semibold text-slate-800" readonly>
                                <button type="button" onclick="updateQty('<?= $drink_id ?>', 1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-slate-600 hover:text-red-600 hover:bg-red-50 shadow-sm transition">+</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Floating Action Button (Only for logged in users) -->
            <?php if($is_logged_in): ?>
            <div class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50">
                <button type="submit" onclick="return checkCart()" class="flex items-center gap-3 bg-slate-900 text-white px-8 py-4 rounded-full font-bold shadow-2xl hover:bg-red-600 transition-colors duration-300 hover:scale-105 transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Proses Pesanan <span id="cartCount" class="bg-red-600 text-white text-xs px-2 py-1 rounded-full ml-2 hidden">0</span>
                </button>
            </div>
            <?php elseif(!$admin_logged_in): ?>
            <div class="text-center py-10 bg-red-50 rounded-2xl border border-red-100 max-w-2xl mx-auto">
                <p class="text-slate-600 mb-4">Masuk ke akunmu untuk mulai memesan makanan</p>
                <a href="login.php" class="inline-block px-8 py-3 bg-red-600 text-white font-semibold rounded-full hover:bg-red-700 shadow-lg shadow-red-200 transition">Login untuk Pesan</a>
            </div>
            <?php endif; ?>

        </form>
    </main>

    <script>
        let totalItems = 0;
        
        function updateQty(id, change) {
            const input = document.getElementById(id + '-qty');
            let val = parseInt(input.value) || 0;
            
            // Revert previous value from total
            totalItems -= val;
            
            val += change;
            if (val < 0) val = 0;
            input.value = val;
            
            // Add new value to total
            totalItems += val;
            
            const badge = document.getElementById('cartCount');
            if (badge) {
                if (totalItems > 0) {
                    badge.textContent = totalItems;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }
        
        function checkCart() {
            if (totalItems === 0) {
                alert('Pilih minimal 1 menu sebelum memproses pesanan.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

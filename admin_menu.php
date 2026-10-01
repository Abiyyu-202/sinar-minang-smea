<?php
session_start();
if (!isset($_SESSION["nama"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}
include("connection.php");

$menu_items = mysqli_query($link, "SELECT * FROM menu ORDER BY id DESC");
$drinks = mysqli_query($link, "SELECT * FROM minuman ORDER BY id DESC");
?>
<?php
$page_title = "Kelola Menu - Admin Panel";
include 'includes/header.php';
?>
<body class="bg-slate-50 flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white min-h-screen fixed left-0 top-0 hidden md:block z-50">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-red-500 mb-8">Admin Panel</h2>
            <ul class="space-y-3">
                <li><a href="dashboard.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Dashboard</a></li>
                <li><a href="admin_orders.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Pesanan Masuk</a></li>
                <li><a href="admin_menu.php" class="block px-4 py-3 rounded-lg bg-red-600 text-white font-medium shadow-lg shadow-red-900/20">Kelola Menu</a></li>
            </ul>
        </div>
        <div class="absolute bottom-0 w-full p-6 border-t border-slate-800">
            <a href="index.php" target="_blank" class="block px-4 py-2 mb-2 text-center text-sm text-slate-400 hover:text-white">Lihat Website</a>
            <a href="logout.php" class="block px-4 py-2 text-center bg-slate-800 hover:bg-slate-700 rounded-lg text-sm text-red-400 transition">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 md:ml-64 p-8">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Kelola Menu</h1>
                <p class="text-slate-500 mt-1">Tambah, edit, dan hapus hidangan restoran</p>
            </div>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-12">
            <!-- Add Food Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">🍲</span> Tambah/Edit Makanan
                </h2>
                <form id="formMenu" action="proses_menu.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="table" value="menu" />
                    <input type="hidden" name="form_type" value="menu" />
                    <input type="hidden" name="id" id="menuId" />
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Makanan</label>
                        <input type="text" id="menuName" name="menu" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                        <input type="text" id="menuPriceDisplay" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" onkeyup="formatRupiah(this, 'menuPrice')">
                        <input type="hidden" id="menuPrice" name="harga">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Singkat</label>
                        <textarea id="menuDescription" name="deskripsi" required rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Upload Gambar atau Link URL (Pilih Salah Satu)</label>
                        <div class="space-y-2">
                            <input type="file" id="menuImage" name="gambar" accept="image/*" class="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
                            <input type="url" id="menuImageUrl" name="gambar_url" placeholder="https://contoh.com/gambar.jpg" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah gambar.</p>
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white font-medium py-3 rounded-lg hover:bg-slate-800 transition">Simpan Makanan</button>
                </form>
            </div>

            <!-- Add Drink Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">🍹</span> Tambah/Edit Minuman
                </h2>
                <form id="formDrink" action="proses_menu.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="table" value="minuman" />
                    <input type="hidden" name="form_type" value="minuman" />
                    <input type="hidden" name="id" id="drinkId" />
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Minuman</label>
                        <input type="text" id="drinkName" name="minuman" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                        <input type="text" id="drinkPriceDisplay" required class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" onkeyup="formatRupiah(this, 'drinkPrice')">
                        <input type="hidden" id="drinkPrice" name="harga">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Singkat</label>
                        <textarea id="drinkDescription" name="deskripsi" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Upload Gambar atau Link URL (Pilih Salah Satu)</label>
                        <div class="space-y-2">
                            <input type="file" id="drinkImage" name="gambar" accept="image/*" class="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                            <input type="url" id="drinkImageUrl" name="gambar_url" placeholder="https://contoh.com/gambar.jpg" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah gambar.</p>
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white font-medium py-3 rounded-lg hover:bg-slate-800 transition">Simpan Minuman</button>
                </form>
            </div>
        </div>

        <!-- Food List -->
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Katalog Makanan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            <?php while ($item = mysqli_fetch_assoc($menu_items)) : 
                $img_src = strpos($item['gambar'], 'http') === 0 ? htmlspecialchars($item['gambar']) : 'picture/' . htmlspecialchars($item['gambar']);
            ?>
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                <img src="<?= $img_src ?>" class="h-40 w-full object-cover">
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-slate-800 line-clamp-1"><?= htmlspecialchars($item['menu']); ?></h3>
                    <p class="text-red-600 font-semibold text-sm mb-3">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></p>
                    <div class="flex gap-2 mt-auto">
                        <button onclick='editMenu(<?= json_encode($item) ?>)' class="flex-1 bg-slate-100 text-slate-700 py-1.5 rounded text-sm font-medium hover:bg-slate-200 transition">Edit</button>
                        <a href="proses_menu.php?delete=<?= $item['id']; ?>&table=menu&id=<?= $item['id']; ?>" onclick="return confirm('Hapus makanan ini?')" class="flex-1 text-center bg-red-50 text-red-600 py-1.5 rounded text-sm font-medium hover:bg-red-100 transition">Hapus</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Drink List -->
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Katalog Minuman</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php while ($drink = mysqli_fetch_assoc($drinks)) : 
                $img_src = strpos($drink['gambar'], 'http') === 0 ? htmlspecialchars($drink['gambar']) : 'picture/' . htmlspecialchars($drink['gambar']);
            ?>
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                <img src="<?= $img_src ?>" class="h-40 w-full object-cover bg-slate-100">
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-slate-800 line-clamp-1"><?= htmlspecialchars($drink['minuman']); ?></h3>
                    <p class="text-blue-600 font-semibold text-sm mb-3">Rp <?= number_format($drink['harga'], 0, ',', '.'); ?></p>
                    <div class="flex gap-2 mt-auto">
                        <button onclick='editDrink(<?= json_encode($drink) ?>)' class="flex-1 bg-slate-100 text-slate-700 py-1.5 rounded text-sm font-medium hover:bg-slate-200 transition">Edit</button>
                        <a href="proses_menu.php?delete=<?= $drink['id']; ?>&table=minuman&id=<?= $drink['id']; ?>" onclick="return confirm('Hapus minuman ini?')" class="flex-1 text-center bg-red-50 text-red-600 py-1.5 rounded text-sm font-medium hover:bg-red-100 transition">Hapus</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script>
        function formatRupiah(element, hiddenId) {
            let value = element.value.replace(/\D/g, '').toString();
            let hiddenInput = document.getElementById(hiddenId);
            hiddenInput.value = value;
            
            let sisa = value.length % 3;
            let rupiah = value.substr(0, sisa);
            let ribuan = value.substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            element.value = rupiah;
        }

        function setFormattedPrice(rawPrice, displayId, hiddenId) {
            let intPrice = Math.round(parseFloat(rawPrice));
            let hiddenInput = document.getElementById(hiddenId);
            hiddenInput.value = intPrice;
            
            let displayInput = document.getElementById(displayId);
            displayInput.value = intPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function editMenu(item) {
            document.getElementById("menuId").value = item.id;
            document.getElementById("menuName").value = item.menu;
            setFormattedPrice(item.harga, "menuPriceDisplay", "menuPrice");
            document.getElementById("menuDescription").value = item.deskripsi || "";
            document.getElementById("menuImageUrl").value = item.gambar.indexOf('http') === 0 ? item.gambar : "";
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
        
        function editDrink(item) {
            document.getElementById("drinkId").value = item.id;
            document.getElementById("drinkName").value = item.minuman;
            setFormattedPrice(item.harga, "drinkPriceDisplay", "drinkPrice");
            document.getElementById("drinkDescription").value = item.deskripsi || "";
            document.getElementById("drinkImageUrl").value = item.gambar.indexOf('http') === 0 ? item.gambar : "";
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    </script>
</body>
</html>

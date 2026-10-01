<?php
session_start();
if (!isset($_SESSION["nama"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}
include("connection.php");

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($link, $_POST['status']);
    
    $update_query = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    mysqli_query($link, $update_query);
    
    header("Location: admin_orders.php");
    exit;
}

// Fetch all orders
$query = "SELECT * FROM orders ORDER BY tanggal DESC";
$result = mysqli_query($link, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Dashboard</title>
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
                <li><a href="dashboard.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Dashboard</a></li>
                <li><a href="admin_orders.php" class="block px-4 py-3 rounded-lg bg-red-600 text-white font-medium shadow-lg shadow-red-900/20">Pesanan Masuk</a></li>
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
                <h1 class="text-3xl font-bold text-slate-900">Pesanan Masuk</h1>
                <p class="text-slate-500 mt-1">Kelola dan update status pesanan pelanggan</p>
            </div>
        </header>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Order ID & Waktu</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Pelanggan</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Total Harga</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status Saat Ini</th>
                            <th class="px-6 py-4 text-sm font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $badge_class = 'bg-red-50 text-red-600 border-red-200'; // Baru Masuk
                            if ($row['status'] == 'Sedang Diproses') $badge_class = 'bg-orange-50 text-orange-600 border-orange-200';
                            if ($row['status'] == 'Selesai') $badge_class = 'bg-green-50 text-green-600 border-green-200';
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800"><?= htmlspecialchars($row['order_id']) ?></div>
                                <div class="text-xs text-slate-500 mt-1"><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_pelanggan']) ?></div>
                                <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($row['metode_pembayaran']) ?></div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border <?= $badge_class ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <?php if ($row['status'] != 'Selesai'): ?>
                                    <form method="POST" class="m-0">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                        
                                        <?php if ($row['status'] == 'Baru Masuk' || $row['status'] == 'Pending'): ?>
                                            <input type="hidden" name="status" value="Sedang Diproses">
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md shadow-red-200 transition">Terima & Proses</button>
                                        <?php elseif ($row['status'] == 'Sedang Diproses'): ?>
                                            <input type="hidden" name="status" value="Selesai">
                                            <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-800 shadow-md shadow-slate-200 transition">Tandai Selesai</button>
                                        <?php endif; ?>
                                    </form>
                                    <?php else: ?>
                                        <span class="text-sm font-medium text-slate-400 italic">Pesanan Selesai</span>
                                    <?php endif; ?>

                                    <a href="invoice.php?id=<?= $row['order_id'] ?>" target="_blank" class="text-slate-400 hover:text-red-600 transition" title="Lihat Struk">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">📝</div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Pesanan</h3>
                <p class="text-slate-500">Saat ini belum ada pesanan yang masuk dari pelanggan.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>

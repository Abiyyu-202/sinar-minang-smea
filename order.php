<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orders = [];
    $total = 0;
    
    require_once 'connection.php';
    
    $menu_query = mysqli_query($link, "SELECT menu as nama, harga FROM menu");
    $menuPrices = [];
    while ($row = mysqli_fetch_assoc($menu_query)) {
        $key = strtolower(str_replace(' ', '_', $row['nama']));
        $menuPrices[$key] = $row['harga'];
    }
    
    $minuman_query = mysqli_query($link, "SELECT minuman as nama, harga FROM minuman");
    while ($row = mysqli_fetch_assoc($minuman_query)) {
        $key = strtolower(str_replace(' ', '_', $row['nama']));
        $menuPrices[$key] = $row['harga'];
    }

    foreach ($_POST as $item => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0 && array_key_exists($item, $menuPrices)) {
            $price = $menuPrices[$item];
            $subtotal = $price * $quantity;
            
            $orders[$item] = [
                'quantity' => $quantity,
                'price' => $price,
                'total' => $subtotal
            ];
            $total += $subtotal;
        }
    }

    $_SESSION['orders'] = $orders;
    $_SESSION['total'] = $total;
}
?>
<?php
$page_title = "Keranjang Pesanan - Sinar Minang SMEA";
include 'includes/header.php';
?>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="index.php" class="text-2xl font-bold text-red-600 flex items-center gap-2">
                    Sinar Minang
                </a>
                <div class="flex items-center space-x-6">
                    <a href="menu.php" class="text-slate-600 hover:text-red-600 font-medium transition">Menu</a>
                    <a href="logout.php" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-full transition">Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-12">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-900 p-8 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2">Keranjang Pesanan</h1>
                    <p class="text-slate-400 text-sm">Periksa kembali pesanan Anda sebelum melakukan pembayaran.</p>
                </div>
            </div>
            
            <div class="p-8">
                <?php if (isset($_SESSION['orders']) && !empty($_SESSION['orders'])): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="py-4 text-slate-500 font-medium">Menu</th>
                                    <th class="py-4 text-slate-500 font-medium text-center">Jumlah</th>
                                    <th class="py-4 text-slate-500 font-medium text-right">Harga Satuan</th>
                                    <th class="py-4 text-slate-500 font-medium text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($_SESSION['orders'] as $item => $details): ?>
                                    <tr>
                                        <td class="py-4 font-semibold text-slate-800"><?php echo ucwords(str_replace('_', ' ', $item)); ?></td>
                                        <td class="py-4 text-center">
                                            <span class="inline-block bg-slate-100 px-3 py-1 rounded-lg text-sm font-bold text-slate-700"><?php echo $details['quantity']; ?>x</span>
                                        </td>
                                        <td class="py-4 text-right text-slate-500">Rp <?php echo number_format($details['price'], 0, ',', '.'); ?></td>
                                        <td class="py-4 text-right font-bold text-slate-800">Rp <?php echo number_format($details['total'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-slate-200">
                                    <td colspan="3" class="py-6 text-right font-medium text-slate-500 text-lg">Total Pembayaran:</td>
                                    <td class="py-6 text-right font-extrabold text-red-600 text-2xl">Rp <?php echo number_format($_SESSION['total'], 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-8 border-t border-slate-100">
                        <a href="menu.php" class="flex-1 text-center py-4 rounded-xl border border-slate-200 font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Tambah Pesanan Lain
                        </a>
                        <a href="konfirmasi.php" class="flex-1 text-center py-4 rounded-xl bg-red-600 font-semibold text-white hover:bg-red-700 shadow-lg shadow-red-200 transition">
                            Lanjut ke Pembayaran
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">🛒</div>
                        <h2 class="text-2xl font-bold text-slate-800 mb-2">Keranjang Kosong</h2>
                        <p class="text-slate-500 mb-8">Anda belum memasukkan menu apapun ke dalam keranjang.</p>
                        <a href="menu.php" class="inline-block bg-slate-900 text-white px-8 py-3 rounded-full font-semibold hover:bg-slate-800 transition">Mulai Memesan</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
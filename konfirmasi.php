<?php
session_start();
include("connection.php");

// Redirect jika tidak ada pesanan
if (!isset($_SESSION['orders']) || empty($_SESSION['orders'])) {
    header('Location: order.php');
    exit;
}

$total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;

$user_email = '';
if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
    $user_email = $_SESSION['email'];
} elseif (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $res = mysqli_query($link, "SELECT email FROM users WHERE id = $uid");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $user_email = $row['email'];
        $_SESSION['email'] = $user_email; // cache it
    }
}

// Handle form submission (Checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($link, $_POST['nama']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $metode = mysqli_real_escape_string($link, $_POST['payment_method']);
    
    // Generate unique Order ID
    $order_id_str = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    
    mysqli_begin_transaction($link);
    try {
        $query = "INSERT INTO orders (order_id, nama_pelanggan, email, metode_pembayaran, total_harga, status) 
                  VALUES ('$order_id_str', '$nama', '$email', '$metode', $total, 'Baru Masuk')";
        if (!mysqli_query($link, $query)) throw new Exception(mysqli_error($link));
        
        $order_pk = mysqli_insert_id($link);
        
        foreach ($_SESSION['orders'] as $item => $details) {
            $nama_item = mysqli_real_escape_string($link, ucwords(str_replace('_', ' ', $item)));
            $jumlah = (int)$details['quantity'];
            $harga = (float)$details['price'];
            $subtotal = (float)$details['total'];
            
            $q_item = "INSERT INTO order_items (order_id, nama_item, jumlah, harga_satuan, subtotal) 
                       VALUES ($order_pk, '$nama_item', $jumlah, $harga, $subtotal)";
            if (!mysqli_query($link, $q_item)) throw new Exception(mysqli_error($link));
        }
        
        mysqli_commit($link);
        
        unset($_SESSION['orders']);
        unset($_SESSION['total']);
        
        header("Location: invoice.php?id=" . $order_id_str);
        exit;
        
    } catch (Exception $e) {
        mysqli_rollback($link);
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>
<?php
$page_title = "Checkout - Sinar Minang SMEA";
include 'includes/header.php';
?>
<body class="bg-slate-50 text-slate-800 min-h-screen">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="index.php" class="text-2xl font-bold text-red-600 flex items-center gap-2">
                    Sinar Minang
                </a>
            </div>
        </div>
    </nav>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-white/95 z-50 hidden flex-col items-center justify-center">
        <div class="spinner border-4 border-slate-200 rounded-full w-16 h-16 mb-4"></div>
        <h2 class="text-2xl font-bold text-slate-800">Memverifikasi Pembayaran...</h2>
        <p class="text-slate-500 mt-2">Mohon tunggu sebentar, jangan tutup halaman ini.</p>
    </div>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="mb-8">
            <a href="order.php" class="text-sm font-medium text-slate-500 hover:text-red-600 flex items-center gap-1 transition">
                &larr; Kembali ke Keranjang
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-8">Checkout Pesanan</h1>
                
                <?php if(isset($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-8"><?= $error ?></div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    
                    <!-- Form Kolom Kiri -->
                    <div>
                        <form id="checkoutForm" method="POST" action="konfirmasi.php" class="space-y-6">
                            
                            <div>
                                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Informasi Akun</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                                        <input type="text" name="nama" value="<?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : '' ?>" readonly class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Email (Tujuan Struk)</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>" readonly class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed outline-none">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Pembayaran</h2>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Metode</label>
                                    <select name="payment_method" id="paymentMethod" onchange="toggleQris()" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all appearance-none bg-slate-50">
                                        <option value="Tunai">Bayar di Kasir (Tunai)</option>
                                        <option value="QRIS">QRIS / E-Wallet (OVO, Gopay, Dana)</option>
                                        <option value="Transfer Bank">Transfer Bank Virtual Account</option>
                                    </select>
                                </div>
                                
                                <div id="qrisBox" class="hidden mt-6 text-center p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300">
                                    <p class="font-bold text-slate-800 mb-4">Scan QR Code Berikut</p>
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=SinarMinangSMEA_DummyQRIS" alt="QRIS" class="mx-auto rounded-xl shadow-sm mb-4">
                                    <p class="text-sm text-slate-500">Gunakan aplikasi m-banking atau e-wallet pilihan Anda.</p>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- Order Summary Kolom Kanan -->
                    <div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 h-full flex flex-col">
                            <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-200 pb-2">Ringkasan Pesanan</h2>
                            
                            <div class="space-y-4 mb-6 flex-1">
                                <?php foreach ($_SESSION['orders'] as $item => $details): ?>
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-slate-800"><?php echo ucwords(str_replace('_', ' ', $item)); ?></p>
                                            <p class="text-sm text-slate-500"><?php echo $details['quantity']; ?>x @ Rp <?php echo number_format($details['price'], 0, ',', '.'); ?></p>
                                        </div>
                                        <p class="font-bold text-slate-800">Rp <?php echo number_format($details['total'], 0, ',', '.'); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="border-t border-slate-200 pt-4 mb-6">
                                <div class="flex justify-between items-center text-lg">
                                    <span class="font-medium text-slate-600">Total Bayar</span>
                                    <span class="font-extrabold text-2xl text-red-600">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <button type="button" onclick="processCheckout()" class="w-full py-4 rounded-xl bg-red-600 font-bold text-white text-lg hover:bg-red-700 shadow-lg shadow-red-200 transition-all flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleQris() {
            const method = document.getElementById('paymentMethod').value;
            document.getElementById('qrisBox').classList.toggle('hidden', method !== 'QRIS');
        }
        
        function processCheckout() {
            const form = document.getElementById('checkoutForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            document.getElementById('loadingOverlay').classList.remove('hidden');
            document.getElementById('loadingOverlay').classList.add('flex');
            
            setTimeout(() => {
                form.submit();
            }, 2000);
        }
    </script>
</body>
</html>
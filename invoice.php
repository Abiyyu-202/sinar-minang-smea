<?php
session_start();
include("connection.php");

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = mysqli_real_escape_string($link, $_GET['id']);
$query = "SELECT * FROM orders WHERE order_id = '$order_id'";
$result = mysqli_query($link, $query);

if (mysqli_num_rows($result) === 0) {
    die("Pesanan tidak ditemukan.");
}

$order = mysqli_fetch_assoc($result);
$order_pk = $order['id'];

$q_items = "SELECT * FROM order_items WHERE order_id = $order_pk";
$items_res = mysqli_query($link, $q_items);
?>
<?php
$page_title = 'Struk Pesanan - ' . htmlspecialchars($order['order_id']);
include 'includes/header.php';
?>
<body>

    <div>
        <div class="receipt">
            <div class="header">
                <h1>Sinar Minang SMEA</h1>
                <p>Jl. Ahmad Yani No.135A, Magelang Utara</p>
                <p>Telp: (021) 123-4567</p>
            </div>
            
            <div class="info">
                <p>Order ID : <?= htmlspecialchars($order['order_id']) ?></p>
                <p>Tanggal  : <?= date('d/m/Y H:i', strtotime($order['tanggal'])) ?></p>
                <p>Nama     : <?= htmlspecialchars($order['nama_pelanggan']) ?></p>
                <p>Metode   : <?= htmlspecialchars($order['metode_pembayaran']) ?></p>
                <p>Status   : <strong><?= htmlspecialchars($order['status']) ?></strong></p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th class="amt">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_item']) ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td class="amt"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="totals">
                <p class="grand-total">
                    <span>Total Bayar</span>
                    <span>Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></span>
                </p>
            </div>
            
            <div class="footer">
                <p>Terima kasih telah memesan.</p>
                <p>Harap simpan struk ini sebagai bukti pembayaran/pesanan.</p>
            </div>
        </div>
        
        <a href="index.php" class="btn-home">Kembali ke Beranda</a>
    </div>

</body>
</html>

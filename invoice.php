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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan - <?= htmlspecialchars($order['order_id']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="./Gambar/sinar_minang_smea.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #e0e0e0; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; font-family: 'Poppins', sans-serif; }
        
        .receipt {
            background: #fff; width: 100%; max-width: 400px; padding: 40px 30px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-radius: 5px;
            position: relative; font-family: 'Courier Prime', monospace; color: #333;
        }
        
        /* Zigzag effect at bottom of receipt */
        .receipt::after {
            content: ''; position: absolute; bottom: -10px; left: 0; width: 100%; height: 10px;
            background: linear-gradient(-45deg, transparent 33.33%, #fff 33.33%, #fff 66.66%, transparent 66.66%),
                        linear-gradient(45deg, transparent 33.33%, #fff 33.33%, #fff 66.66%, transparent 66.66%);
            background-size: 20px 20px;
        }
        
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px dashed #ccc; padding-bottom: 20px; }
        .header h1 { font-family: 'Poppins', sans-serif; font-size: 1.5rem; margin-bottom: 5px; color: #c62828; }
        .header p { font-size: 0.9rem; color: #666; }
        
        .info { margin-bottom: 20px; font-size: 0.9rem; line-height: 1.5; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        th { text-align: left; border-bottom: 1px dashed #ccc; padding-bottom: 10px; }
        td { padding: 8px 0; }
        .amt { text-align: right; }
        
        .totals { border-top: 2px dashed #ccc; padding-top: 20px; margin-bottom: 30px; }
        .totals p { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1rem; }
        .totals .grand-total { font-size: 1.2rem; font-weight: bold; }
        
        .footer { text-align: center; font-size: 0.85rem; color: #666; }
        
        .btn-home {
            display: block; width: 100%; padding: 15px; text-align: center; background: #c62828; color: white;
            text-decoration: none; font-family: 'Poppins', sans-serif; font-weight: 600; border-radius: 30px; margin-top: 40px; transition: 0.3s;
        }
        .btn-home:hover { background: #b71c1c; }
    </style>
</head>
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

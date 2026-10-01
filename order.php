<?php
// order.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the order form
    $orders = [];
    $total = 0;
    
    // Menu items with their prices
    $menuPrices = [
        'rendang' => 13000,
        'ayam_bakar' => 12000,
        'kikil' => 16000,
        'ayam_gulai' => 12000,
        'telur_dadar' => 7000,
        'kerupuk_kulit' => 6000,
        'gulai_otak' => 13000,
        'dendeng_balado' => 13000,
        'daun_singkong' => 8000,
        'sayur_nangka' => 8000,
        'nasi_putih' => 7000,
        'ayam_goreng' => 12000,
        'air_putih' => 3000,
        'es_teh' => 4000,
        'es_jeruk' => 4000,
        'kopi' => 4000
    ];
    
    foreach ($_POST as $item => $quantity) {
        if ($quantity > 0 && array_key_exists($item, $menuPrices)) {
            $orders[$item] = [
                'quantity' => $quantity,
                'price' => $menuPrices[$item],
                'total' => $quantity * $menuPrices[$item]
            ];
            $total += $orders[$item]['total'];
        }
    }
    
    // Store orders in session
    $_SESSION['orders'] = $orders;
    $_SESSION['total'] = $total;
    
    // Redirect to order summary
    header('Location: order.php');
    exit;
}

// Display order summary
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary - Sinar Minang SMEA</title>
    <link rel="stylesheet" href="./CSS/menu.css">
    <link rel="icon" href="./Gambar/sinar_minang_smea.png">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.html" class="brand">Sinar Minang SMEA</a>
            <ul class="nav-menu">
                <li><a href="indeks.php">Beranda</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="#contact">Kontak</a></li>
            </ul>
            <div class="nav-right">
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="order-summary">
            <div class="container">
                <h1>Ringkasan Pesanan Anda</h1>
                
                <?php if (isset($_SESSION['orders']) && !empty($_SESSION['orders'])): ?>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['orders'] as $item => $details): ?>
                                <tr>
                                    <td><?php echo ucwords(str_replace('_', ' ', $item)); ?></td>
                                    <td><?php echo $details['quantity']; ?></td>
                                    <td>Rp<?php echo number_format($details['price'], 0, ',', '.'); ?></td>
                                    <td>Rp<?php echo number_format($details['total'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>Rp<?php echo number_format($_SESSION['total'], 0, ',', '.'); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="order-actions">
                        <a href="menu.php" class="cta-btn">Tambah Pesanan</a>
                        <a href="konfirmasi.php" class="cta-btn confirm-btn">Konfirmasi Pesanan</a>
                    </div>
                <?php else: ?>
                    <p>Anda belum memesan apapun.</p>
                    <br>
                    <a href="menu.php" class="cta-btn">Kembali ke Menu</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <style>
        .order-summary {
            padding: 50px 0;
            background-color: #FFEBEE;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .order-table th, .order-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #FFCDD2;
        }
        
        .order-table th {
            background-color: #C62828;
            color: white;
        }
        
        .order-table tr:hover {
            background-color: #FFCDD2;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #FFEBEE;
        }
        
        .text-right {
            text-align: right;
        }
        
        .order-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .confirm-btn {
            background-color: #ffffff;
        }
        
        .confirm-btn:hover {
            background-color: #FFABAB;
        }
    </style>
</body>
</html>
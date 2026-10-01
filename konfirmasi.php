<?php
// konfirmasi.php
session_start();

// Redirect jika tidak ada pesanan
if (!isset($_SESSION['orders']) || empty($_SESSION['orders'])) {
    header('Location: order.php');
    exit;
}

// Hitung total dari session jika ada
$total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - Sinar Minang SMEA</title>
    <link rel="stylesheet" href="./CSS/menu.css">
    <link rel="icon" href="./Gambar/sinar_minang_smea.png">
    <style>
        body {
            background-color: #FFEBEE;
            font-family: 'Poppins', sans-serif;
        }

        .payment-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .payment-method {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .card-details {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .qris-container {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .qris-container img {
            max-width: 250px;
            margin-bottom: 10px;
        }

        .order-summary {
            margin-top: 30px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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

        .total-row {
            font-weight: bold;
            background-color: #FFEBEE;
        }

        .btn {
            padding: 12px 24px;
            background-color: #C62828;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #8E0000;
        }

        .btn-confirm {
            background-color: #4CAF50;
            width: 100%;
            margin-top: 20px;
        }

        .btn-confirm:hover {
            background-color: #388E3C;
        }

        .navbar {
            background-color: #C62828;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
        }

        .nav-menu li {
            margin-left: 20px;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: #FFCDD2;
        }

        .logout-btn {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #8E0000;
        }
    </style>
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

    <div class="payment-container">
        <h1>Konfirmasi Pembayaran</h1>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="Masukkan email Anda" required>
        </div>
        
        <div class="payment-method">
            <label for="payment-method">Metode Pembayaran:</label>
            <select id="payment-method" onchange="togglePaymentDetails()">
                <option value="tunai">Bayar Tunai</option>
                <option value="debit">Kartu Debit</option>
                <option value="qris">QRIS</option>
            </select>
            
            <div id="card-details" class="card-details">
                <div class="form-group">
                    <label>Nama pada Kartu</label>
                    <input type="text" placeholder="Nama lengkap">
                </div>
                <div class="form-group">
                    <label>Nomor Kartu</label>
                    <input type="text" placeholder="1234 5678 9012 3456">
                </div>
                <div class="form-group">
                    <label>Tanggal Kadaluarsa</label>
                    <input type="text" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" placeholder="123">
                </div>
            </div>
            
            <div id="qris-container" class="qris-container">
                <img src="qris_dummy.png" alt="QR Code Pembayaran">
                <p>Scan QR code di atas menggunakan aplikasi e-wallet Anda</p>
            </div>
        </div>
        
        <div class="order-summary">
            <h2>Ringkasan Pesanan</h2>
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
                        <td colspan="3"><strong>Total:</strong></td>
                        <td><strong>Rp<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <button class="btn btn-confirm" onclick="processPayment()">Konfirmasi Pembayaran</button>
    </div>

    <script>
        function togglePaymentDetails() {
            const method = document.getElementById('payment-method').value;
            const cardDetails = document.getElementById('card-details');
            const qrisContainer = document.getElementById('qris-container');
            
            cardDetails.style.display = method === 'debit' ? 'block' : 'none';
            qrisContainer.style.display = method === 'qris' ? 'block' : 'none';
        }
        
        function processPayment() {
            const email = document.getElementById('email').value;
            
            if (!email) {
                alert('Harap masukkan email Anda');
                return;
            }
            
            alert('Terima kasih telah membayar, struk akan dikirim ke email Anda');
            window.location.href = 'menu.php';
        }
    </script>
</body>
</html>
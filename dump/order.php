<?php 
    session_start();
    if (!isset($_SESSION["nama"])) {
        header("Location: login.php");
    }

    include("connection.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order our Food</title>
    <link rel="icon" href="./Gambar/sinar_minang_smea.png">
    <style>
        /* --- Global Styles --- */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFF3F3;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        /* --- Navbar --- */
        .navbar {
            background-color: #C62828;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            width: 100%;
            max-width: 1200px;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 2rem;
            font-weight: bold;
            text-decoration: none;
            color: #FFFFFF;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav-menu a {
            text-decoration: none;
            color: #FFFFFF;
            font-weight: 500;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .nav-menu a:hover {
            color: #FFCDD2;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            padding: 0.5em 1em;
            background-color: #FFFFFF;
            color: #C62828;
            border-radius: 20px;
            font-weight: bold;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #FFCDD2;
            color: #B71C1C;
        }

        /* --- Form Styling --- */
        form {
            max-width: 500px;
            background-color: #FFFFFF;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        form input[type="text"],
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #CCC;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        form input:focus,
        form select:focus {
            border-color: #C62828;
            outline: none;
        }

        form button[type="submit"] {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background-color: #C62828;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button[type="submit"]:hover {
            background-color: #B71C1C;
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
                <li><a href="index.php#about">Tentang Kami</a></li>
                <li><a href="index.php#contact">Kontak</a></li>
            </ul>
            <div class="nav-right">
                <a href="./logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <form action="" method="post">
        <label for="menu">Nama Menu : </label>
        <input type="text" name="menu" id="menu" autocomplete="off">

        <br>

        <label for="harga">Harga : </label>
        <input type="text" name="harga" id="harga" autocomplete="off">

        <br>

        <label for="jumlah">Jumlah : </label>
        <input type="text" name="jumlah" id="jumlah" autocomplete="off">
        
        <br>

        <label for="alamat">Alamat Pengiriman</label>
        <input type="text" name="alamat" id="alamat" autocomplete="off">

        <label for="bayar">Metode Pembayaran : </label>
        <select name="bayar" id="bayar">
            <option value="Metode Pembayaran">-Pilih Metode Pembayaran-</option>
            <option value="Transfer">Transfer Bank</option>
            <option value="COD">Cash on Delivery (Bayar di Tempat)</option>
        </select>

        <br>

        <button type="submit">Pesan</button>
    </form>
</body>

</html>
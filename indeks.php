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
    <title>Sinar Minang SMEA</title>
    <link rel="icon" href="./Gambar/sinar_minang_smea.png">
    <link rel="stylesheet" href="./CSS/navbar.css">
    <link rel="stylesheet" href="./CSS/landing.css">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="" class="brand">Sinar Minang SMEA</a>
            <ul class="nav-menu">
                <li><a href="">Beranda</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="#about">Tentang Kami</a></li>
                <li><a href="#contact">Kontak</a></li>
            </ul>
            <div class="nav-right">
                <form class="search-form">
                </form>
                <a href="./logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di Sinar Minang SMEA</h1>
            <p>Rasakan cita rasa asli Minang langsung dari dapur kami.</p>
            <div class="hero-buttons">
                <a href="./menu.php" class="btn-primary">Lihat Menu</a>
                <a href="./order.php" class="btn-secondary">Pesan Sekarang</a>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section class="about-us" id="about">
        <div class="section-container">
            <h2>Tentang Kami</h2>
            <img src="./Gambar/restoran_padang.jpeg" alt="Restoran Padang" class="about-image">
            <p>Sinar Minang SMEA hadir untuk memberikan pengalaman kuliner khas Minang yang autentik. Kami menyajikan berbagai hidangan Padang lezat dengan bumbu yang kaya dan cita rasa yang tak terlupakan. Dengan pelayanan yang ramah dan suasana nyaman, Rumah Makan Padang adalah pilihan tepat untuk menikmati masakan khas Sumatera Barat.</p>
        </div>
    </section>

    <!-- Kontak Section -->
    <section class="contact" id="contact">
        <div class="section-container">
            <h2>Kontak Kami</h2>
            <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3760.388746407088!2d110.2215536747624!3d-7.450312092560889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a85e87d740c9b%3A0x688883a50dc2f187!2sSMK%20Negeri%202%20Magelang!5e1!3m2!1sid!2sid!4v1747181437173!5m2!1sid!2sid" width="700px"  height="500px" box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) border-radius: 15px; style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe><br>
            <!-- <img src="./Gambar/lokasi_icon.png" alt="Lokasi" class="contact-image"> -->
            <p>Hubungi kami untuk informasi lebih lanjut atau pemesanan.</p>
            <ul class="contact-list">
                <li>📞 Telepon: (021) 123-4567</li>
                <li>📍 Alamat: Jl. Ahmad Yani No.135A, Kramat Sel., Kec. Magelang Utara, Kota Magelang, Jawa Tengah 59155</li>
                <li>✉️ Email: info@smsmea.com</li>
            </ul>
        </div>
    </section>


    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <p>&copy; 2025 Sinar Minang SMEA. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>
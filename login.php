<?php
// Include koneksi ke MySQL dari file connection.php
include("connection.php");

$pesan_error = ""; // Start a session for login state management

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil username dan password dari form login
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Filter input menggunakan mysqli_real_escape_string
    $username = mysqli_real_escape_string($link, $username);
    $password = mysqli_real_escape_string($link, $password);

    $password_sha1 = sha1($password);

    // Cek apakah username ada di tabel admin
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password_sha1'";
    $result = mysqli_query($link, $query);
    
    if (mysqli_num_rows($result) == 0) {
        // User tidak ditemukan, tampilkan error
        $pesan_error = "Username tidak ditemukan";
    } 

    if (empty($pesan_error)) {
        session_start();
        $_SESSION["nama"] = $username;
        $_SESSION["role"] = "admin"; // Set role as admin
        header("Location: indeks.php"); // Perbaiki spasi setelah Location
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinar Minang SMEA</title>
    <link rel="stylesheet" href="./CSS/login.css">
    <link rel="icon" href="./Gambar/sinar_minang_smea.png">
</head>

<body>
    <div class="container">
        <h1>Selamat Datang</h1>
        <h2>Silahkan Login Dahulu</h2>
        <div class="login-form">
            <h3>Login</h3>
            <?php if (isset($pesan_error)): ?>
                <div class="error"><?= $pesan_error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="username">Username :</label>
                <input type="text" id="username" name="username" required autocomplete="off">

                <label for="password">Password :</label>
                <input type="password" id="password" name="password" required autocomplete="off">

                <button type="submit">Log In</button>
            </form>
        </div>
    </div>
</body>

</html>
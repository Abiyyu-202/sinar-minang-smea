<?php
session_start();

if (!isset($_SESSION["nama"])) {
    header("Location: login.php");
}

include("connection.php");

// Handle Delete Operation
if (isset($_GET['delete'], $_GET['table'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $table = mysqli_real_escape_string($link, $_GET['table']);

    if (!in_array($table, ['menu', 'minuman'])) {
        die('Invalid table name');
    }

    mysqli_begin_transaction($link);
    try {
        // Delete the image file first
        $query = "SELECT gambar FROM $table WHERE id = $id";
        $result = mysqli_query($link, $query);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $image_path = "picture/" . $row['gambar'];
            if (file_exists($image_path) && is_writable($image_path)) {
                unlink($image_path);
            }
        }

        // Delete the record
        $query = "DELETE FROM $table WHERE id = $id";
        if (!mysqli_query($link, $query)) {
            throw new Exception(mysqli_error($link));
        }
        mysqli_commit($link);

        header('Location: admin_menu.php');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($link);
        die("Error deleting record: " . $e->getMessage());
    }
}

// Handle Add/Edit Operation for Food Menu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] === "menu") {
    $table = 'menu';
    $name_field = 'menu';

    if (!isset($_POST['harga']) || !is_numeric($_POST['harga']) || $_POST['harga'] <= 0) {
        die("Error: Price must be a positive number.");
    }

    mysqli_begin_transaction($link);
    try {
        $name = mysqli_real_escape_string($link, $_POST[$name_field]);
        $price = (float) $_POST['harga'];
        $description = isset($_POST['deskripsi']) ? mysqli_real_escape_string($link, $_POST['deskripsi']) : '';

        $hasId = isset($_POST['id']) && !empty($_POST['id']);
        
        $image_changed = false;
        $image = null;
        
        if (!empty($_POST['gambar_url'])) {
            $image = filter_var($_POST['gambar_url'], FILTER_SANITIZE_URL);
            $image_changed = true;
        } elseif (!empty($_FILES['gambar']['name'])) {
            if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Upload error: " . $_FILES['gambar']['error']);
            }
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception("Error: Only JPG, JPEG, PNG, WEBP & GIF files are allowed.");
            }
            if ($_FILES['gambar']['size'] > 5000000) {
                throw new Exception("Error: File size too large (max 5MB).");
            }
            $image = uniqid() . '.' . $ext;
            $target = "picture/" . $image;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                throw new Exception("Failed to upload file.");
            }
            $image_changed = true;
        }

        if ($hasId) {
            $id = (int)$_POST['id'];

            if ($image_changed) {
                $query = "UPDATE $table SET $name_field = ?, harga = ?, deskripsi = ?, gambar = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdssi", $name, $price, $description, $image, $id);
            } else {
                $query = "UPDATE $table SET $name_field = ?, harga = ?, deskripsi = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $description, $id);
            }
        } else {
            if (!$image_changed) {
                throw new Exception("Image is required for new items.");
            }
            $query = "INSERT INTO $table ($name_field, harga, deskripsi, gambar) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "sdss", $name, $price, $description, $image);
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($link));
        }
        mysqli_stmt_close($stmt);
        mysqli_commit($link);

        header('Location: admin_menu.php');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($link);
        die("Database error: " . $e->getMessage());
    }
}

// Handle Add/Edit Operation for Drinks Menu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] === "minuman") {
    $table = 'minuman';
    $name_field = 'minuman';

    if (!isset($_POST['harga']) || !is_numeric($_POST['harga']) || $_POST['harga'] <= 0) {
        die("Error: Price must be a positive number.");
    }

    mysqli_begin_transaction($link);
    try {
        $name = mysqli_real_escape_string($link, $_POST[$name_field]);
        $price = (float) $_POST['harga'];
        $description = isset($_POST['deskripsi']) ? mysqli_real_escape_string($link, $_POST['deskripsi']) : '';

        $hasId = isset($_POST['id']) && !empty($_POST['id']);
        
        $image_changed = false;
        $image = null;
        
        if (!empty($_POST['gambar_url'])) {
            $image = filter_var($_POST['gambar_url'], FILTER_SANITIZE_URL);
            $image_changed = true;
        } elseif (!empty($_FILES['gambar']['name'])) {
            if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Upload error: " . $_FILES['gambar']['error']);
            }
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception("Error: Only JPG, JPEG, PNG, WEBP & GIF files are allowed.");
            }
            if ($_FILES['gambar']['size'] > 5000000) {
                throw new Exception("Error: File size too large (max 5MB).");
            }
            $image = uniqid() . '.' . $ext;
            $target = "picture/" . $image;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                throw new Exception("Failed to upload file.");
            }
            $image_changed = true;
        }

        if ($hasId) {
            $id = (int)$_POST['id'];

            if ($image_changed) {
                $query = "UPDATE $table SET $name_field = ?, harga = ?, deskripsi = ?, gambar = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdssi", $name, $price, $description, $image, $id);
            } else {
                $query = "UPDATE $table SET $name_field = ?, harga = ?, deskripsi = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $description, $id);
            }
        } else {
            if (!$image_changed) {
                throw new Exception("Image is required for new items.");
            }
            $query = "INSERT INTO $table ($name_field, harga, deskripsi, gambar) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "sdss", $name, $price, $description, $image);
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($link));
        }
        mysqli_stmt_close($stmt);
        mysqli_commit($link);

        header('Location: admin_menu.php');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($link);
        die("Database error: " . $e->getMessage());
    }
}

// Fetch all menu items and drinks
?>

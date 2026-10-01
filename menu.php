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

        header('Location: menu.php');
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
        $image_changed = !empty($_FILES['gambar']['name']);

        if ($hasId) {
            $id = (int)$_POST['id'];

            if ($image_changed) {
                if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Upload error: " . $_FILES['gambar']['error']);
                }
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
                }
                if ($_FILES['gambar']['size'] > 5000000) {
                    throw new Exception("Error: File size too large (max 5MB).");
                }
                $image = uniqid() . '.' . $ext;
                $target = "picture/" . $image;
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    throw new Exception("Failed to upload file.");
                }
                $query = "UPDATE $table SET $name_field = ?, harga = ?, deskripsi = ?, gambar = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdssi", $name, $price, $description, $image, $id);
            } else {
                $query = "UPDATE $table SET $name_field = ?, harga = ?, deskripsi = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $description, $id);
            }
        } else {
            if (empty($_FILES['gambar']['name'])) {
                throw new Exception("Image is required for new items.");
            }
            if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Upload error: " . $_FILES['gambar']['error']);
            }
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
            }
            if ($_FILES['gambar']['size'] > 5000000) {
                throw new Exception("Error: File size too large (max 5MB).");
            }
            $image = uniqid() . '.' . $ext;
            $target = "picture/" . $image;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                throw new Exception("Failed to upload file.");
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

        header('Location: menu.php');
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

        $hasId = isset($_POST['id']) && !empty($_POST['id']);
        $image_changed = !empty($_FILES['gambar']['name']);

        if ($hasId) {
            $id = (int)$_POST['id'];

            if ($image_changed) {
                if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Upload error: " . $_FILES['gambar']['error']);
                }
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
                }
                if ($_FILES['gambar']['size'] > 5000000) {
                    throw new Exception("Error: File size too large (max 5MB).");
                }
                $image = uniqid() . '.' . $ext;
                $target = "picture/" . $image;
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    throw new Exception("Failed to upload file.");
                }
                $query = "UPDATE $table SET $name_field = ?, harga = ?, gambar = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $image, $id);
            } else {
                $query = "UPDATE $table SET $name_field = ?, harga = ? WHERE id = ?";
                $stmt = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($stmt, "sdi", $name, $price, $id);
            }
        } else {
            if (empty($_FILES['gambar']['name'])) {
                throw new Exception("Image is required for new items.");
            }
            if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Upload error: " . $_FILES['gambar']['error']);
            }
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
            }
            if ($_FILES['gambar']['size'] > 5000000) {
                throw new Exception("Error: File size too large (max 5MB).");
            }
            $image = uniqid() . '.' . $ext;
            $target = "picture/" . $image;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                throw new Exception("Failed to upload file.");
            }
            $query = "INSERT INTO $table ($name_field, harga, gambar) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "sds", $name, $price, $image);
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($link));
        }
        mysqli_stmt_close($stmt);
        mysqli_commit($link);

        header('Location: menu.php');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($link);
        die("Database error: " . $e->getMessage());
    }
}

// Fetch all menu items and drinks
try {
    $menu_items = mysqli_query($link, "SELECT * FROM menu ORDER BY id DESC");
    $drinks = mysqli_query($link, "SELECT * FROM minuman ORDER BY id DESC");
    if (!$menu_items || !$drinks) {
        throw new Exception(mysqli_error($link));
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sinar Minang SMEA - Manage Menu and Drinks</title>
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      background-color: #FFF5F5;
      color: #333;
      line-height: 1.4;
      min-height: 100vh;
    }
    .navbar {
      background-color: #C62828;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      margin-bottom: 20px;
    }
    .nav-container {
      width: 90%;
      max-width: 1200px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .brand {
      color: #FFF;
      font-size: 2rem;
      font-weight: bold;
      text-decoration: none;
    }
    .nav-menu {
      display: flex;
      gap: 2rem;
      list-style: none;
    }
    .nav-menu a {
      color: #FFF;
      font-weight: 500;
      font-size: 1.1rem;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    .nav-menu a:hover {
      color: #FFCDD2;
    }
    .nav-right {
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    .logout-btn {
      background: #FFF;
      color: #C62828;
      border-radius: 20px;
      padding: 0.5em 1em;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s, color 0.3s;
      border: none;
      cursor: pointer;
    }
    .logout-btn:hover {
      background: #FFCDD2;
      color: #B71C1C;
    }
    .container {
      max-width: 1200px;
      margin: 0 auto 50px;
      padding: 0 15px;
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
      justify-content: center;
    }
    h1, h2 {
      color: #B71C1C;
      margin-bottom: 15px;
      width: 100%;
      text-align: center;
    }
    form {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(198,40,40,0.2);
      max-width: 100%;
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    form .form-group {
      display: flex;
      flex-direction: column;
    }
    form label {
      font-weight: bold;
      margin-bottom: 6px;
      color: #7B1FA2;
    }
    form input[type="text"],
    form input[type="number"],
    form textarea,
    form input[type="file"] {
      padding: 10px 12px;
      border-radius: 8px;
      border: 1.8px solid #E91E63;
      font-size: 1rem;
      color: #333;
      transition: border-color 0.3s ease;
    }
    form input[type="text"]:focus,
    form input[type="number"]:focus,
    form textarea:focus,
    form input[type="file"]:focus {
      outline: none;
      border-color: #9C27B0;
      box-shadow: 0 0 5px rgba(156, 39, 176, 0.5);
    }
    form textarea {
      resize: vertical;
      min-height: 80px;
    }
    form button.save-btn {
      background: #C62828;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      font-size: 1.1rem;
      transition: background-color 0.3s ease;
      align-self: center;
      width: 60%;
    }
    form button.save-btn:hover {
      background: #B71C1C;
    }
    .cards-container {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
      width: 100%;
    }
    .card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(198,40,40,0.2);
      overflow: hidden;
      width: 280px;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(198,40,40,0.3);
    }
    .card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }
    .card-content {
      padding: 15px 20px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .card-content h3 {
      font-size: 1.4rem;
      color: #B71C1C;
      margin-bottom: 8px;
      min-height: 48px;
    }
    .card-content p.description {
      font-size: 0.9rem;
      color: #555;
      flex-grow: 1;
      overflow: hidden;
      max-height: 66px;
      margin-bottom: 10px;
    }
    .card-content p.price {
      font-size: 1.2rem;
      font-weight: bold;
      color: #C62828;
      margin-bottom: 10px;
    }
    .card-actions {
      display: flex;
      justify-content: space-between;
      padding: 10px 20px 15px 20px;
    }
    .action-btn {
      background: #C62828;
      border: none;
      border-radius: 10px;
      color: white;
      font-weight: bold;
      cursor: pointer;
      padding: 8px 15px;
      font-size: 0.9rem;
      transition: background-color 0.3s ease;
      user-select: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .action-btn.edit {
      background: #4CAF50;
    }
    .action-btn.edit:hover {
      background: #388E3C;
    }
    .action-btn.delete:hover {
      background: #B71C1C;
    }
    .action-btn.delete {
      background: #F44336;
    }
    @media (max-width: 1024px) {
      .container {
        flex-direction: column;
        align-items: center;
      }
      form {
        max-width: 90%;
      }
    }
    .order-controls {
      display: flex;
      align-items: center;
      margin-top: 15px;
    }
    .quantity-btn {
            width: 30px;
            height: 30px;
            background-color: #C62828;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:hover {
            background-color: #B71C1C;
        }
        
        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            margin: 0 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-order {
          text-align: center;
          margin-top: 30px;
        }

        .order-btn {
          background-color: #C62828;
          color: white;
          border: none;
          border-radius: 5px;
          font-size: 18px;
          padding: 12px 30px;
          cursor: pointer;
          font-weight: bold;
          transition: background-color 0.3s ease;
        }

        .order-btn:hover {
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
        <li><a href="#contact">Kontak</a></li>
      </ul>
      <div class="nav-right">
        <a href="./logout.php" class="logout-btn">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <section aria-labelledby="foodMenuTitle">
      <h2 id="foodMenuTitle">Add/Edit Food Menu</h2>
      <form id="formMenu" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="table" value="menu" />
        <input type="hidden" name="form_type" value="menu" />
        <input type="hidden" name="id" id="menuId" />

        <div class="form-group">
          <label for="menuName">Name:</label>
          <input type="text" id="menuName" name="menu" required maxlength="100" />
        </div>
        <div class="form-group">
          <label for="menuPrice">Price:</label>
          <input type="number" id="menuPrice" name="harga" required min="0" step="1000" />
        </div>
        <div class="form-group">
          <label for="menuDescription">Description:</label>
          <textarea id="menuDescription" name="deskripsi" maxlength="500"></textarea>
        </div>
        <div class="form-group">
          <label for="menuImage">Image:</label>
          <input type="file" id="menuImage" name="gambar" accept="image/*" />
          <small>Leave empty if you don't want to change the image when editing.</small>
        </div>
        <button type="submit" class="save-btn">Save Food Item</button>
      </form>
    </section>

    <section aria-labelledby="drinkMenuTitle">
      <h2 id="drinkMenuTitle">Add/Edit Drinks Menu</h2>
      <form id="formDrink" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="table" value="minuman" />
        <input type="hidden" name="form_type" value="minuman" />
        <input type="hidden" name="id" id="drinkId" />

        <div class="form-group">
          <label for="drinkName">Name:</label>
          <input type="text" id="drinkName" name="minuman" required maxlength="100" />
        </div>
        <div class="form-group">
          <label for="drinkPrice">Price:</label>
          <input type="number" id="drinkPrice" name="harga" required min="0" step="1000" />
        </div>
        <div class="form-group">
          <label for="drinkImage">Image:</label>
          <input type="file" id="drinkImage" name="gambar" accept="image/*" />
          <small>Leave empty if you don't want to change the image when editing.</small>
        </div>
        <button type="submit" class="save-btn">Save Drink</button>
      </form>
    </section>

    <form action="order.php" method="post" id="orderForm" style="width: 100%;">
  <h1>Food Menu Items</h1>
  <div class="cards-container" id="menuCards">
      <?php while ($item = mysqli_fetch_assoc($menu_items)) : 
        $item_id = strtolower(str_replace(' ', '_', $item['menu']));
      ?>
        <div class="card" data-table="menu">
          <img src="picture/<?= htmlspecialchars($item['gambar']); ?>" alt="<?= htmlspecialchars($item['menu']); ?>" />
          <div class="card-content">
            <h3><?= htmlspecialchars($item['menu']); ?></h3>
            <p class="description"><?= nl2br(htmlspecialchars($item['deskripsi'])); ?></p>
            <p class="price">Rp<?= number_format($item['harga'], 0, ',', '.'); ?></p>
            <div class="order-controls">
                <button type="button" class="quantity-btn minus" onclick="updateQuantity('<?= $item_id ?>', -1)">-</button>
                <input type="number" id="<?= $item_id ?>-qty" name="<?= $item_id ?>" value="0" min="0" class="quantity-input">
                <button type="button" class="quantity-btn plus" onclick="updateQuantity('<?= $item_id ?>', 1)">+</button>
            </div>
          </div>
          <div class="card-actions">
            <button type="button" class="action-btn edit" onclick='populateForm("menu", <?= json_encode($item) ?>)'>Edit</button>
            <a href="?delete=<?= $item['id']; ?>&table=menu&id=<?= $item['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
  </div>

  <h1>Drinks Menu Items</h1>
  <div class="cards-container" id="drinkCards">
      <?php while ($drink = mysqli_fetch_assoc($drinks)) : 
        $drink_id = strtolower(str_replace(' ', '_', $drink['minuman']));
      ?>
        <div class="card" data-table="minuman">
          <img src="picture/<?= htmlspecialchars($drink['gambar']); ?>" alt="<?= htmlspecialchars($drink['minuman']); ?>" />
          <div class="card-content">
            <h3><?= htmlspecialchars($drink['minuman']); ?></h3>
            <p class="price">Rp<?= number_format($drink['harga'], 0, ',', '.'); ?></p>
            <div class="order-controls">
                <button type="button" class="quantity-btn minus" onclick="updateQuantity('<?= $drink_id ?>', -1)">-</button>
                <input type="number" id="<?= $drink_id ?>-qty" name="<?= $drink_id ?>" value="0" min="0" class="quantity-input">
                <button type="button" class="quantity-btn plus" onclick="updateQuantity('<?= $drink_id ?>', 1)">+</button>
            </div>
          </div>
          <div class="card-actions">
            <button type="button" class="action-btn edit" onclick='populateForm("minuman", <?= json_encode($drink) ?>)'>Edit</button>
            <a href="?delete=<?= $drink['id']; ?>&table=minuman&id=<?= $drink['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
  </div>
  
  <div class="submit-order">
      <button type="submit" class="order-btn">Pesan Sekarang</button>
  </div>
</form>

  <script>
    function populateForm(type, item) {
      if (type === "menu") {
        const formId = document.getElementById("menuId");
        const formName = document.getElementById("menuName");
        const formPrice = document.getElementById("menuPrice");
        const formDescription = document.getElementById("menuDescription");
        const formImage = document.getElementById("menuImage");

        formId.value = item.id || "";
        formName.value = item.menu || "";
        formPrice.value = item.harga || "";
        formDescription.value = item.deskripsi || "";
        formImage.value = "";

        window.scrollTo({ top: formId.form.offsetTop - 20 || 0, behavior: "smooth" });
      } else if (type === "minuman") {
        const formId = document.getElementById("drinkId");
        const formName = document.getElementById("drinkName");
        const formPrice = document.getElementById("drinkPrice");
        const formImage = document.getElementById("drinkImage");

        formId.value = item.id || "";
        formName.value = item.minuman || "";
        formPrice.value = item.harga || "";
        formImage.value = "";

        window.scrollTo({ top: formId.form.offsetTop - 20 || 0, behavior: "smooth" });
      }
    }
    function updateQuantity(item, change) {
      const input = document.getElementById(item + '-qty');
      let currentVal = parseInt(input.value);
      if (isNaN(currentVal)) currentVal = 0;
      let newValue = currentVal + change;
      if (newValue < 0) newValue = 0;
      input.value = newValue;
    }
  </script>
</body>
</html>


<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["nama"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

include("connection.php");

// Check database connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create upload directory if it doesn't exist
if (!file_exists("picture")) {
    if (!mkdir("picture", 0777, true)) {
        die("Error: Failed to create upload directory.");
    }
} else {
    // Verify directory is writable
    if (!is_writable("picture")) {
        die("Error: Upload directory is not writable.");
    }
}

// Handle Delete Operation
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $table = mysqli_real_escape_string($link, $_GET['table']);
    
    // Validate table name
    if (!in_array($table, ['menu', 'minuman'])) {
        die("Invalid table name");
    }
    
    mysqli_begin_transaction($link);
    try {
        // Delete the image file first
        $query = "SELECT gambar FROM $table WHERE id = $id";
        $result = mysqli_query($link, $query);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $image_path = "picture/" . $row['gambar'];
            if (file_exists($image_path) && is_writable($image_path)) {
                if (!unlink($image_path)) {
                    throw new Exception("Failed to delete image file");
                }
            }
        }
        
        // Delete the record
        $query = "DELETE FROM $table WHERE id = $id";
        if (!mysqli_query($link, $query)) {
            throw new Exception(mysqli_error($link));
        }
        
        mysqli_commit($link);
        header("Location: manage_menu.php");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($link);
        die("Error deleting record: " . $e->getMessage());
    }
}

// Handle Add/Edit Operation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug logging
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    $table = mysqli_real_escape_string($link, $_POST['table']);
    
    // Validate table name
    if (!in_array($table, ['menu', 'minuman'])) {
        die("Invalid table name");
    }
    
    $name_field = ($table == 'menu') ? 'menu' : 'minuman';
    
    // Validate price
    if (!is_numeric($_POST['harga']) || $_POST['harga'] <= 0) {
        die("Error: Price must be a positive number.");
    }
    
    mysqli_begin_transaction($link);
    try {
        if (isset($_POST['id'])) { // Edit
            $id = (int)$_POST['id'];
            $name = mysqli_real_escape_string($link, $_POST[$name_field]);
            $price = (float)$_POST['harga'];
            $description = isset($_POST['deskripsi']) ? mysqli_real_escape_string($link, $_POST['deskripsi']) : '';
            
            if (!empty($_FILES['gambar']['name'])) {
                // Validate file upload
                if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                    $errors = [
                        UPLOAD_ERR_INI_SIZE => "File too large (PHP.ini)",
                        UPLOAD_ERR_FORM_SIZE => "File too large (Form)",
                        UPLOAD_ERR_PARTIAL => "Partial upload",
                        UPLOAD_ERR_NO_FILE => "No file uploaded",
                        UPLOAD_ERR_NO_TMP_DIR => "No temp directory",
                        UPLOAD_ERR_CANT_WRITE => "Cannot write file",
                        UPLOAD_ERR_EXTENSION => "Upload stopped by extension"
                    ];
                    throw new Exception("Upload error: " . ($errors[$_FILES['gambar']['error']] ?? "Unknown error"));
                }
                
                // Validate file type
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
                }
                
                // Validate file size (5MB max)
                if ($_FILES['gambar']['size'] > 5000000) {
                    throw new Exception("Error: File size is too large. Max 5MB allowed.");
                }
                
                // Generate unique filename
                $image = uniqid() . '.' . $ext;
                $target = "picture/" . $image;
                
                // Verify upload
                if (!is_uploaded_file($_FILES['gambar']['tmp_name'])) {
                    throw new Exception("File upload verification failed");
                }
                
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    throw new Exception("Error: Failed to upload file. Check directory permissions.");
                }
                
                if ($table == 'menu') {
                    $query = "UPDATE $table SET menu=?, harga=?, deskripsi=?, gambar=? WHERE id=?";
                    $stmt = mysqli_prepare($link, $query);
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . mysqli_error($link));
                    }
                    mysqli_stmt_bind_param($stmt, "sdssi", $name, $price, $description, $image, $id);
                } else {
                    $query = "UPDATE $table SET minuman=?, harga=?, gambar=? WHERE id=?";
                    $stmt = mysqli_prepare($link, $query);
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . mysqli_error($link));
                    }
                    mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $image, $id);
                }
            } else {
                if ($table == 'menu') {
                    $query = "UPDATE $table SET menu=?, harga=?, deskripsi=? WHERE id=?";
                    $stmt = mysqli_prepare($link, $query);
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . mysqli_error($link));
                    }
                    mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $description, $id);
                } else {
                    $query = "UPDATE $table SET minuman=?, harga=? WHERE id=?";
                    $stmt = mysqli_prepare($link, $query);
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . mysqli_error($link));
                    }
                    mysqli_stmt_bind_param($stmt, "sdi", $name, $price, $id);
                }
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_error($link));
            }
            mysqli_stmt_close($stmt);
            
        } else { // Add
            $name = mysqli_real_escape_string($link, $_POST[$name_field]);
            $price = (float)$_POST['harga'];
            $description = isset($_POST['deskripsi']) ? mysqli_real_escape_string($link, $_POST['deskripsi']) : '';
            
            // Validate file upload
            if (empty($_FILES['gambar']['name'])) {
                throw new Exception("Error: Image is required for new items.");
            }
            
            if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                $errors = [
                    UPLOAD_ERR_INI_SIZE => "File too large (PHP.ini)",
                    UPLOAD_ERR_FORM_SIZE => "File too large (Form)",
                    UPLOAD_ERR_PARTIAL => "Partial upload",
                    UPLOAD_ERR_NO_FILE => "No file uploaded",
                    UPLOAD_ERR_NO_TMP_DIR => "No temp directory",
                    UPLOAD_ERR_CANT_WRITE => "Cannot write file",
                    UPLOAD_ERR_EXTENSION => "Upload stopped by extension"
                ];
                throw new Exception("Upload error: " . ($errors[$_FILES['gambar']['error']] ?? "Unknown error"));
            }
            
            // Validate file type
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
            }
            
            // Validate file size (5MB max)
            if ($_FILES['gambar']['size'] > 5000000) {
                throw new Exception("Error: File size is too large. Max 5MB allowed.");
            }
            
            // Generate unique filename
            $image = uniqid() . '.' . $ext;
            $target = "picture/" . $image;
            
            // Verify upload
            if (!is_uploaded_file($_FILES['gambar']['tmp_name'])) {
                throw new Exception("File upload verification failed");
            }
            
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                throw new Exception("Error: Failed to upload file. Check directory permissions.");
            }
            
            if ($table == 'menu') {
                $query = "INSERT INTO $table (menu, harga, deskripsi, gambar) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($link, $query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . mysqli_error($link));
                }
                mysqli_stmt_bind_param($stmt, "sdss", $name, $price, $description, $image);
            } else {
                $query = "INSERT INTO $table (minuman, harga, gambar) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($link, $query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . mysqli_error($link));
                }
                mysqli_stmt_bind_param($stmt, "sds", $name, $price, $image);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_error($link));
            }
            $insert_id = mysqli_insert_id($link);
            error_log("New item created with ID: " . $insert_id);
            mysqli_stmt_close($stmt);
        }
        
        mysqli_commit($link);
        header("Location: manage_menu.php");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($link);
        // Log the full error
        error_log("Database error: " . $e->getMessage());
        // Display to user
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

<?php
$page_title = "Manage Menu - Sinar Minang SMEA";
include 'includes/header.php';
?>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Menu</h1>
            <a href="menu.php" class="back-btn">Back to Menu</a>
        </div>

        <!-- Food Menu Section -->
        <div class="section">
            <h2>Food Menu</h2>
            <button class="add-btn" onclick="showModal('menu')">Add New Food Item</button>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($menu_items)) { ?>
                        <tr>
                            <td><img src="picture/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['menu']) ?>" class="preview"></td>
                            <td><?= htmlspecialchars($item['menu']) ?></td>
                            <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($item['deskripsi']) ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick='editItem(<?= json_encode($item) ?>, "menu")'>Edit</button>
                                <a href="?delete=<?= $item['id'] ?>&table=menu" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Drinks Menu Section -->
        <div class="section">
            <h2>Drinks Menu</h2>
            <button class="add-btn" onclick="showModal('minuman')">Add New Drink</button>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($drink = mysqli_fetch_assoc($drinks)) { ?>
                        <tr>
                            <td><img src="picture/<?= htmlspecialchars($drink['gambar']) ?>" alt="<?= htmlspecialchars($drink['minuman']) ?>" class="preview"></td>
                            <td><?= htmlspecialchars($drink['minuman']) ?></td>
                            <td>Rp<?= number_format($drink['harga'], 0, ',', '.') ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick='editItem(<?= json_encode($drink) ?>, "minuman")'>Edit</button>
                                <a href="?delete=<?= $drink['id'] ?>&table=minuman" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Add/Edit -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Add New Item</h2>
            <form id="itemForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="itemId" name="id">
                <input type="hidden" id="tableType" name="table">
                
                <div class="form-group">
                    <label id="nameLabel">Name:</label>
                    <input type="text" id="itemName" name="menu" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" id="itemPrice" name="harga" required min="0" step="1000">
                </div>
                
                <div id="descriptionGroup" class="form-group">
                    <label>Description:</label>
                    <textarea id="itemDescription" name="deskripsi" maxlength="500"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Image:</label>
                    <input type="file" id="itemImage" name="gambar" accept="image/*">
                    <small>Max size: 5MB. Allowed types: JPG, JPEG, PNG, GIF</small>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showModal(type) {
            document.getElementById('modal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New ' + (type === 'menu' ? 'Food Item' : 'Drink');
            document.getElementById('tableType').value = type;
            document.getElementById('nameLabel').textContent = type === 'menu' ? 'Menu Name:' : 'Drink Name:';
            document.getElementById('descriptionGroup').style.display = type === 'menu' ? 'block' : 'none';
            
            // Set the correct name attribute based on the type
            const nameInput = document.getElementById('itemName');
            nameInput.name = type === 'menu' ? 'menu' : 'minuman';
            nameInput.value = '';
            
            // Clear form
            document.getElementById('itemForm').reset();
            document.getElementById('itemId').value = '';
        }

        function hideModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function editItem(item, type) {
            showModal(type);
            document.getElementById('modalTitle').textContent = 'Edit ' + (type === 'menu' ? 'Food Item' : 'Drink');
            document.getElementById('itemId').value = parseInt(item.id);
            const nameInput = document.getElementById('itemName');
            nameInput.name = type === 'menu' ? 'menu' : 'minuman';
            nameInput.value = type === 'menu' ? 
                item.menu.replace(/[<>]/g, '') : 
                item.minuman.replace(/[<>]/g, '');
            document.getElementById('itemPrice').value = parseFloat(item.harga);
            if (type === 'menu') {
                document.getElementById('itemDescription').value = 
                    (item.deskripsi || '').replace(/[<>]/g, '');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('modal')) {
                hideModal();
            }
        }
    </script>
</body>
</html>
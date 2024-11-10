<?php
session_start();
$is_admin = true; // This should be determined dynamically

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proj";

if ($is_admin) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $itemName = $_POST['item_name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $stock = $_POST['stock'];

            // Handle file upload
            $image = $_FILES['image'];
            $imagePath = null;

            if ($image['error'] === UPLOAD_ERR_OK) {
                // Specify the directory to save the uploaded image
                $uploadDir = 'fileImg/';
                $imagePath = $uploadDir . basename($image['name']);

                // Move the uploaded file to the specified directory
                if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                    echo "Failed to upload image.";
                }
            }

            // Insert the item data into the database, including the image path
            $sql = "INSERT INTO inventory (item_name, price, description, stock, img_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$itemName, $price, $description, $stock, $imagePath]);

            // Set a session variable to indicate success
            $_SESSION['success_message'] = "Item added successfully!";
        }
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    echo "You do not have permission to access this page.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item to Inventory</title>
    <link rel="stylesheet" href="add_item.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
        }
        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 240px;
            background-color: #28a745;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            color: #fff;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: #ffffff;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-weight: 500;
        }
        .sidebar .nav-link:hover {
            background-color: #218838;
        }
        /* Content styling */
        .content {
            margin-left: 260px;
            padding: 40px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-container label {
            font-weight: 500;
            color: #333;
        }
        .form-container .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .form-container .submit-btn:hover {
            background-color: #218838;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <nav class="nav flex-column">
        <a href="admin.php" class="nav-link">Dashboard</a>
        <a href="auditreport.php" class="nav-link">Audit</a>
        <a href="salesreport.php" class="nav-link">Reports</a>
        <a href="add_item.php" class="nav-link">Add Item</a>
        <a href="login.php" class="nav-link">Logout</a>
    </nav>
</div>

<div class="content">
    <div class="form-container">
        <h2>Add New Item to Inventory</h2>
        <form action="add_item.php" method="post" enctype="multipart/form-data">
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required step="0.01">

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>

            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit" class="submit-btn">Add Item</button>
            <a href="admin.php" class="back-link">Go back to Home</a>
        </form>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="modal" id="successModal">
        <div class="modal-content">
            <h2>Success!</h2>
            <p><?php echo $_SESSION['success_message']; ?></p>
            <button onclick="closeModal()">Done</button>
        </div>
    </div>
    <script>
        document.getElementById("successModal").style.display = "flex";
        function closeModal() {
            document.getElementById("successModal").style.display = "none";
            <?php unset($_SESSION['success_message']); ?>
            window.location.href = "add_item.php";
        }
    </script>
    <?php endif; ?>
</div>

</body>
</html>

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
                $uploadDir = 'fileImg/'; // Change to 'fileImg'
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
    <link rel="stylesheet" href="add_item.css"> <!-- Link to your CSS file -->
</head>
<body>
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
    
    <!-- Success Modal -->
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="modal" id="successModal">
        <div class="modal-content">
            <h2>Success!</h2>
            <p><?php echo $_SESSION['success_message']; ?></p>
            <button onclick="closeModal()">Done</button>
        </div>
    </div>
    <script>
        // Display the success modal
        document.getElementById("successModal").style.display = "block";

        function closeModal() {
            document.getElementById("successModal").style.display = "none";
            // Clear the success message from the session
            <?php unset($_SESSION['success_message']); ?>
            window.location.href = "inventory.php"; // Redirect to admin page after closing
        }
    </script>
    <?php endif; ?>

</body>
</html>

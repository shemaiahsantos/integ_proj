<?php
// Include database connection
include 'conx.php'; // Make sure this path is correct

// Handle new order insertion (this would typically be done in your order processing code)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assume you have the following data from the order
    $productName = $_POST['product_name']; // Get this from your order data
    $quantity = $_POST['quantity'];         // Get this from your order data
    $salesAmount = $_POST['sales_amount'];  // Get this from your order data

    // Insert sales data into the salesreport table
    $insertQuery = "INSERT INTO salesreport (product_name, total_quantity, total_sales, sale_date) VALUES (:product_name, :total_quantity, :total_sales, NOW())";
    $stmtInsert = $conn->prepare($insertQuery);
    $stmtInsert->bindParam(':product_name', $productName);
    $stmtInsert->bindParam(':total_quantity', $quantity);
    $stmtInsert->bindParam(':total_sales', $salesAmount);
    $stmtInsert->execute();
}

// Fetch total sales statistics
$totalSalesQuery = "SELECT SUM(total_sales) as total_sales, SUM(total_quantity) as total_quantity FROM salesreport";
$stmtTotal = $conn->prepare($totalSalesQuery);
$stmtTotal->execute();
$totalSalesStats = $stmtTotal->fetch(PDO::FETCH_ASSOC);

// Fetch best-selling products for the current month
$month = date('Y-m'); // Current year and month
$bestSellersQuery = "
    SELECT product_name, SUM(total_quantity) as total_quantity 
    FROM salesreport 
    WHERE DATE_FORMAT(sale_date, '%Y-%m') = :month 
    GROUP BY product_name 
    ORDER BY total_quantity DESC 
    LIMIT 1"; // Fetch only the best seller
$stmtBestSellers = $conn->prepare($bestSellersQuery);
$stmtBestSellers->bindParam(':month', $month);
$stmtBestSellers->execute();
$bestSeller = $stmtBestSellers->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Page styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 240px;
            background-color: #28a745; /* green shade */
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
        /* Content area */
        .content {
            margin-left: 260px;
            padding: 40px;
        }
        .content h1 {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
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

<!-- Content Area -->
<div class="content">
    <h1>Sales Statistics</h1>

    <!-- Total Sales Statistics -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Total Sales Statistics</h4>
            <p><strong>Total Quantity Sold:</strong> <?php echo htmlspecialchars($totalSalesStats['total_quantity']); ?></p>
            <p><strong>Total Sales Amount:</strong> <?php echo htmlspecialchars($totalSalesStats['total_sales']); ?></p>
        </div>
    </div>

    <!-- Best Seller of the Month -->
    <h4>Best Seller This Month</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Quantity Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bestSeller)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bestSeller['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($bestSeller['total_quantity']); ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center">No best-selling products this month.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

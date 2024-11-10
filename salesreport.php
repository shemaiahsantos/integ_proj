<?php
// Include database connection
include 'conx.php'; // Make sure this path is correct

// Fetch sales data
$query = "SELECT * FROM salesreport ORDER BY product_ID ASC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Page styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        /* Sidebar styles */
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
            color: #ffffff;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: #ffffff;
            font-size: 1rem;
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

        /* Table styles */
        .table {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table thead th {
            background-color: #28a745;
            color: #ffffff;
            font-weight: bold;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
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
        <a href="salesreport.php" class="nav-link active">Reports</a>
        <a href="add_item.php" class="nav-link">Add Item</a>
        <a href="login.php" class="nav-link">Logout</a>
    </nav>
</div>

<!-- Content Area -->
<div class="content">
    <h1>Sales Report</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Total Quantity</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_ID']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_sales']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No sales data available.</td>
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

<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Customer Orders</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total (₱)</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order["id"]); ?></td>
                    <td><?php echo htmlspecialchars($order["username"]); ?></td>
                    <td><?php echo htmlspecialchars(number_format($order["total"], 2)); ?></td>
                    <td><?php echo htmlspecialchars($order["payment_method"]); ?></td>
                    <td><?php echo htmlspecialchars($order["status"]); ?></td>
                    <td><?php echo htmlspecialchars($order["created_at"]); ?></td>
                    <td><a href="view_order.php?order_id=<?php echo $order["id"]; ?>">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

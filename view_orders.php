<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit();
}

$orderId = $_GET["order_id"];
$stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Order Details</h1>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Price (₱)</th>
                <th>Quantity</th>
                <th>Subtotal (₱)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item["item_name"]); ?></td>
                    <td><?php echo htmlspecialchars(number_format($item["price"], 2)); ?></td>
                    <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                    <td><?php echo htmlspecialchars(number_format($item["price"] * $item["quantity"], 2)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

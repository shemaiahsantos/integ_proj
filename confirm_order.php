<?php
session_start();

// Check if user has come from checkout.php
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Clear the cart
$_SESSION["cart"] = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Thank you for your order!</h1>
    <p>Your order has been successfully placed.</p>
    <p>We will notify you once it is ready for delivery.</p>

    <a href="user.php">Return to Home</a>
</body>
</html>

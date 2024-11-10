<?php
session_start();
require_once 'conx.php'; // Include your database connection script here

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$total = 0;
$cartItems = isset($_SESSION["cart"]) ? $_SESSION["cart"] : [];

if (empty($cartItems)) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paymentMethod = $_POST["payment_method"];

    if ($paymentMethod === "gcash") {
        header("Location: gcash_payment.php");
        exit();
    } elseif ($paymentMethod === "cod") {
        // Order confirmed, clear cart, and add to the database
        $_SESSION["cart"] = [];
        $orderConfirmed = true;

        // Insert each item in the cart into the database
        foreach ($cartItems as $item) {
            $productName = $item["item_name"];
            $quantity = (int)$item["quantity"]; // Cast quantity to integer
            $price = (float)$item["price"]; // Cast price to float
            $subtotal = $quantity * $price;

            // Debugging output
            echo "Inserting: $productName, Quantity: $quantity, Sales: $subtotal<br>";

            // Prepare and execute the SQL insert statement
            $stmt = $conn->prepare("INSERT INTO salesreport (product_name, total_quantity, total_sales) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                total_quantity = total_quantity + VALUES(total_quantity),
                total_sales = total_sales + VALUES(total_sales)");

            // Try executing and catch any errors
            try {
                $stmt->execute([$productName, $quantity, $subtotal]);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage(); // Print any error message
            }
        }
    } else {
        $error = "Please select a valid payment method.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style3.css">
    <link rel="stylesheet" type="text/css" href="checkout.css">
    <title>Checkout</title>
</head>
<body>
<div class="form-box">
    <h1>Checkout</h1>

    <h2>Order Summary</h2>
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
            <?php foreach ($cartItems as $item): ?>
                <?php
                    $subtotal = $item["price"] * $item["quantity"];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item["item_name"]); ?></td>
                    <td><?php echo htmlspecialchars(number_format($item["price"], 2)); ?></td>
                    <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                    <td><?php echo htmlspecialchars(number_format($subtotal, 2)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-box">Total: ₱<?php echo number_format($total, 2); ?></div>

    <form action="checkout.php" method="POST">
        <h3>Select Payment Method</h3>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="payment-methods">
            <label>
                <input type="radio" name="payment_method" value="gcash" required> GCash
            </label>
            <label>
                <input type="radio" name="payment_method" value="cod"> Cash on Delivery
            </label>
        </div>

        <button type="submit">Confirm and Place Order</button>
    </form>

    <button class="back-button" onclick="window.location.href='cart.php'">Go Back to Cart</button>
</div>

<!-- COD Confirmation Prompt -->
<?php if (isset($orderConfirmed) && $orderConfirmed): ?>
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h1>Thank you for your order!</h1>
        <p>Your order has been successfully placed.</p>
        <p>We will notify you via email once it is ready for delivery.</p>
        <a href="user.php">Return to Home</a>
    </div>
</div>
<script>
    document.getElementById("confirmationModal").style.display = "flex";
</script>
<?php endif; ?>

</body>
</html>

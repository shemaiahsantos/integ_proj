<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }
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
        .content {
            margin-left: 260px;
            padding: 40px;
        }
        .content h1 {
            color: #28a745;
            font-weight: bold;
        }
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
    <title>Shopping Cart</title>
</head>
<body>

<div class="sidebar">
    <h2>User Dashboard</h2>
    <nav class="nav flex-column">
        <a href="user.php" class="nav-link">Home</a>
        <a href="cart.php" class="nav-link">Cart</a>
        <a href="userprof.php" class="nav-link">User Profile</a>
        <button id="logoutButton" class="nav-link" style="background: none; border: none; color: white; padding: 10px;">Logout</button>
    </nav>
</div>

<div class="content">
    <h1>Your Shopping Cart</h1>

    <?php if (!empty($_SESSION["cart"])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price (₱)</th>
                    <th>Quantity</th>
                    <th>Subtotal (₱)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION["cart"] as $item): ?>
                    <?php
                        $subtotal = $item["price"] * $item["quantity"];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item["item_name"]); ?></td>
                        <td><?php echo htmlspecialchars(number_format($item["price"], 2)); ?></td>
                        <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                        <td><?php echo htmlspecialchars(number_format($subtotal, 2)); ?></td>
                        <td>
                            <button onclick="updateCart('<?php echo $item["item_name"]; ?>', 'increase')" class="btn btn-success">+</button>
                            <button onclick="updateCart('<?php echo $item["item_name"]; ?>', 'decrease')" class="btn btn-warning">-</button>
                            <button onclick="updateCart('<?php echo $item["item_name"]; ?>', 'remove')" class="btn btn-danger">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Total: ₱<?php echo number_format($total, 2); ?></h2>
        <button onclick="window.location.href='checkout.php'" class="btn btn-primary">Proceed to Checkout</button>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

<script>
    function updateCart(itemName, action) {
        fetch("update_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "itemName=" + encodeURIComponent(itemName) + "&action=" + action
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update cart display
            } else {
                alert("Error: " + (data.message || "Failed to update cart"));
            }
        })
        .catch(error => console.error("Error:", error));
    }

    document.getElementById("logoutButton").addEventListener("click", function() {
        var confirmLogout = confirm("Are you sure you want to logout?");
        if (confirmLogout) {
            window.location.href = "index.php";
        }
    });
</script>
</body>
</html>

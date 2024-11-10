<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $referenceNumber = $_POST["reference_number"];

    if (!empty($referenceNumber)) {
        $_SESSION["cart"] = [];
        $orderConfirmed = true;
    } else {
        $error = "Please enter your GCash reference number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style3.css">
    <link rel="stylesheet" type="text/css" href="gcash_payment.css">
    <title>GCash Payment</title>
</head>
<body>
<div class="form-box">
    <h1>GCash Payment</h1>

    <p>Scan the QR code below with your GCash app and enter the reference number provided after payment.</p>
    
    <div class="qr-code">
        <img src="fileImg/gcashqr.png" alt="GCash QR Code" />
    </div>

    <form action="gcash_payment.php" method="POST">
    <?php if (isset($error)): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

        <label for="reference_number">Reference Number</label>
        <input type="text" id="reference_number" name="reference_number" required maxlength="6" pattern="\d{6}" title="Please enter exactly 6 digits." oninput="this.value = this.value.replace(/[^0-9]/g, '')">

        <button type="submit">Confirm Payment</button>
    </form>

    <!-- Go Back to Home Button -->
    <button onclick="window.location.href='user.php'" class="back-button">Go Back to Home</button>
</div>

<!-- Success Modal -->
<?php if (isset($orderConfirmed) && $orderConfirmed): ?>
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h1>Thank you for your payment!</h1>
        <p>Your payment has been received. Please wait for the shop to confirm your order. You will be notified once it’s ready for delivery.</p>
        <a href="user.php" class="modal-back-button">Go Back to Home</a>
    </div>
</div>
<script>
    // Display the modal on successful order confirmation
    document.getElementById("confirmationModal").style.display = "flex";
</script>
<?php endif; ?>

</body>
</html>

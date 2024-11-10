<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style2.css">
    <title>User Registration</title>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "client";
$password = "password";
$dbname = "proj";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['address']) && isset($_POST['phone'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $hash = hash('sha256', $pass);

    $stmt = $conn->prepare("INSERT INTO user (Name, Email, Pass, Address, Phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hash, $address, $phone);

    if ($stmt->execute() === TRUE) {
        echo "Registration successful";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<center>
    <form class="form" method="post">
        <h3>User Registration</h3>
        <label>
            <input required placeholder="Name" type="text" class="input" name="name" pattern="[A-Za-z ]+" title="Please enter letters and spaces only">
        </label>
        <label>
            <input required placeholder="Email" type="email" class="input" name="email">
        </label>
        <label>
            <input required type="password" class="input" name="pass" placeholder="Password" pattern="(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z0-9!@#$%^&*]{6,}" title="Password must be at least 6 characters long, contain at least one number, and one special character">
        </label>
        <label>
            <input required placeholder="Address" type="text" class="input" name="address">
        </label>
        <label>
            <input required placeholder="Phone Number" type="tel" class="input" name="phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
        </label>
        <div class="d flex">
            <button type="submit" class="submit">Submit</button>
        </div>
    </form>
    <p class="signin">Already have an account? <a href="login.php">Signin</a></p>
</center>
</body>
</html>

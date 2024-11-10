<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proj";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['address']) && isset($_POST['phone'])) {
        // Gather user inputs
        $name = $_POST['name'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $hash = hash('sha256', $pass);

        // Set the status column to 1 by default
        $status = 1;

        $stmt = $conn->prepare("INSERT INTO sbf (username, email, password, fname, lname, address, phone, status) VALUES (:username, :email, :password, :fname, :lname, :address, :phone, :status)");
        $stmt->bindParam(':username', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':status', $status);

        // Execute the statement
        $stmt->execute();

        // Redirect to user.php after successful registration
        echo "Registration successful.";
        header("Location: login.php");
        exit();
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style2.css">
    <title>User Registration</title>
</head>
<body>
<header>
    <img src="logo.png" alt="Logo" class="logo">
    <nav class="navigation">
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Contact</a>
    </nav>
</header>
<div class="container">
    <form class="form" method="post">
        <h1>Create Account</h1>
        <label>
            <span>Username</span>
            <input required="" placeholder="Username" type="text" class="input" name="name" pattern="[A-Z][a-zA-Z ]{3,}" title="Username must start with a capital letter, followed by letters and spaces (min 4 characters)">
        </label>
        <label>
            <span>Password</span>
            <input required="" placeholder="Password" type="password" class="input" name="pass" pattern=".{6,}" title="Password must be at least 6 characters long">
        </label>
        <label>
            <span>Email</span>
            <input required="" placeholder="Email" type="email" class="input" name="email">
        </label>
        <label>
            <span>First Name</span>
            <input required="" placeholder="First Name" type="text" class="input" name="fname" pattern="[A-Za-z]{1,50}" title="Please enter letters only (max 50 characters)">
        </label>
        <label>
            <span>Last Name</span>
            <input required="" placeholder="Last Name" type="text" class="input" name="lname" pattern="[A-Za-z]{1,50}" title="Please enter letters only (max 50 characters)">
        </label>
        <label>
            <span>Address</span>
            <input required="" placeholder="Address" type="text" class="input" name="address">
        </label>
        <label>
            <span>Phone Number</span>
            <input required="" placeholder="Phone Number" type="tel" class="input" name="phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
        </label>
        <div class="d flex">
            <button type="submit" class="submit">Create Account</button>
        </div>
        <p class="signin">Already have an account? <a href="login.php">Sign in</a></p>
    </form>
</div>
</body>
</html>

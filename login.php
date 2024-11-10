<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proj";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set PDO to throw exceptions on error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if login_attempts and last_attempt_time are set in session
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if (!isset($_SESSION['last_attempt_time'])) {
        $_SESSION['last_attempt_time'] = 0;
    }

    $current_time = time();
    $lockout_time = 30; // 30 seconds

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['pass'])) {
        if ($_SESSION['login_attempts'] >= 3 && ($current_time - $_SESSION['last_attempt_time']) < $lockout_time) {
            $remaining_time = $lockout_time - ($current_time - $_SESSION['last_attempt_time']);
            $errorMessage = "Too many login attempts. Please try again after $remaining_time seconds.";
        } else {
            // Gather user inputs
            $input_username = $_POST['username'];
            $input_password = $_POST['pass'];
            $hashedPassword = hash('sha256', $input_password);

            // Fetch user from database
            $sql = "SELECT * FROM sbf WHERE LOWER(username) = LOWER(:username)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $input_username);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $passwordFromDB = $row['password'];
                $status = $row['status'];

                if ($passwordFromDB === $hashedPassword) {
                    $_SESSION['username'] = $input_username; // Set session variable upon successful login
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['name'] = $row['fname'] . ' ' . $row['lname'];
                    $_SESSION['uID'] = $row['regid'];
                    $_SESSION['address'] = isset($row['address']) ? $row['address'] : ''; // Handle missing address
                    $_SESSION['phone'] = isset($row['phone']) ? $row['phone'] : ''; // Handle missing phone

                    // for login activity
                    $regid = $row['regid'];
                    $activity_type = 'login';
                    $activity_time = date("Y-m-d H:i:s");

                    $sbf_sql = "INSERT INTO user_activity (regid, activity_type, activity_time) VALUES (?, ?, ?)";
                    $sbf_stmt = $conn->prepare($sbf_sql);
                    $sbf_stmt->execute([$regid, $activity_type, $activity_time]);

                    // Reset login attempts upon successful login
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['last_attempt_time'] = 0;

                    // Redirect after successful login
                    if ($status == 2) { // status 2 represents admin
                        header("Location: admin.php");
                    } else {
                        header("Location: user.php");
                    }
                    exit();
                } else {
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt_time'] = time();
                    $errorMessage = "Incorrect password or username!";
                }
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                $errorMessage = "User not found!";
            }
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style1.css">
    <title>Login Form</title>
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
        <div class="form">
            <form method="post">
                <p class="form-title">Sign in</p>
                <div class="input-container">
                    <p class="text-b">Username:</p>
                    <input placeholder="Enter username" type="text" name="username" pattern="[A-Za-z0-9]{4,}" title="Username must be alphanumeric and at least 4 characters long" required>
                </div>
                <div class="input-container">
                    <p class="text-b">Password:</p>
                    <input placeholder="Enter password" type="password" name="pass" pattern="(?=.*\d).{6,}" title="Password must be at least 6 characters long and contain at least one number">
                    <small>Password must be at least 6 characters long and contain at least one number and one special character</small>
                </div>
                <button class="submit" type="submit" pattern=".{8,}" title="Password must be at least 8 characters long">Sign in</button>
                <p class="signup-link">No account? <a href="index.php">Sign up</a></p>
            </form>
        </div>
    </div>
</body>
</html>

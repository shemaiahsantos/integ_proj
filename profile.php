<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user information from the database if the user is logged in
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        $stmt = $conn->prepare("SELECT username, email, lname, fname, img FROM login WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user) {
            $fname = $user['fname'];
            $lname = $user['lname'];
            $email = $user['email'];
            $imageData = $user['img'];
        } else {
            // Redirect user to login page if user not found
            header("Location: login.php");
            exit();
        }
    } else {
        // Redirect user to login page if not logged in
        header("Location: login.php");
        exit();
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>User Profile</title>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">User Profile</h1>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <?php
                            if(isset($imageData) && !empty($imageData)) {
                                $imageSrc = 'data:image/jpeg;base64,'.base64_encode($imageData);
                                echo "<img src='$imageSrc' alt='Profile Picture' class='img-fluid rounded-circle' style='max-width: 200px;'>";
                            } else {
                                echo "<img src='noprofile.png' alt='Profile Picture' class='img-fluid rounded-circle' style='max-width: 200px;'>";
                            }
                            ?>
                        </div>
                        <h4 class="card-title text-center"><?php echo $fname . " " . $lname; ?></h4>
                        <p class="card-text text-center"><?php echo $username; ?></p>
                        <p class="card-text text-center"><?php echo $email; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <button  class="btn btn-danger mt-3 d-block mx-auto"  onclick="window.location.href='user.php'">Go back Home</button>
    </div>
</body>
</html>
